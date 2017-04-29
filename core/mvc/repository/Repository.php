<?php

namespace Core\Mvc\Repository;

use Core\Database\Orm\Schema\Table;
use Core\Mvc\Schema\Schema;
use Core\Database\Database;
use Core\Mvc\Model\Model;
use PDO;


abstract class Repository{

    protected $pdo;
    protected $schema;
    protected $schemaDefinition;
    protected $table;
    protected $model;

    public function __construct(Database $db, string $modelNs, Schema $schema)
    {
        $this->pdo = $db->getConnection();
        $this->schema = $schema;
        $this->table = $this->schema->table();
        $this->model = $modelNs;
    }

    public function getTable(): Table{
        return $this->table;
    }

    public function getSchema(): Schema{
        return $this->schema;
    }

    public function getModel(): Model{
        return (new $this->model($this));
    }


    public function fetch($sql, $param = []){
        $query = $this->pdo->prepare($sql);
        $query->execute($param);
        if(! is_null($this->model)){
            $query->setFetchMode(PDO::FETCH_CLASS, $this->model);
            return $query->fetch();
        }
        return $query->fetch();
    }

    public function fetchAll($sql, $param = []){
        $query = $this->pdo->prepare($sql);
        $query->execute($param);
        if(! is_null($this->model)){
            return $query->fetchAll(PDO::FETCH_CLASS, $this->model);
        }
        return $query->fetchAll();
    }

    public function getLastInsertId(){
        return $this->pdo->lastInsertId();
    }

    public function find($clause, $bind){
        $sql = 'SELECT ' . ($clause['select'] ? is_array($clause['select']) ? implode(', ',$clause['select']) : $clause['select'] : '*') . ' ';
        $sql .= 'FROM ' . ($clause['from'] ? is_array($clause['from']) ? implode(', ', $clause['from']) : $clause['from'] : $this->table) . ' ';
        $sql .= $clause['where'] ? ( 'WHERE ' . (is_array($clause['where']) ? implode(' AND ', $clause['where']) : $clause['where'])) : '';
        $sql .= $clause['orderby'] ? ( 'ORDER BY ' . (is_array($clause['orderby']) ? implode(', ', $clause['orderby']) : $clause['orderby'])) : '';
        $sql .= $clause['groupby'] ? ( 'GROUP BY ' . (is_array($clause['groupby']) ? implode(', ', $clause['groupby']) : $clause['groupby'])) : '';
        $sql .= $clause['having'] ? ( 'HAVING ' . (is_array($clause['having']) ? implode(' AND ', $clause['having']) : $clause['having'])) : '';
        $sql .= $clause['limit'] ? ( 'LIMIT ' . (is_array($clause['limit']) ? implode(', ', $clause['limit']) : $clause['limit'])) : '';
        $sql.= ';';
        return $this->fetchAll($sql, $bind);
    }


    public function getAll(){
        $sql = "SELECT * FROM {$this->table};";
        return $this->fetchAll($sql);
    }

    public function getByField($field, $value){
        $sql = "SELECT * FROM {$this->table} WHERE {$field} = ?;";
        return $this->fetchAll($sql,[$value]);
    }

    public function getByFields(array $fields, array $values){
        $where = implode(' AND ',array_map(function($e){
            return "{$e} = :{$e}";
        }, $fields));
        $sql = "SELECT * FROM {$this->table} WHERE {$where};";
        return $this->fetchAll($sql,[$values]);
    }

    public function getById($id){
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE id = ?;';
        return $this->fetch($sql,[$id]);
    }


    public function persist(Model $o){
        if(is_null($o->getId())){
            return $this->update($o);
        }
        $shouldUpdate = $this->find(
            [
                'select' => ' COUNT(id) as nb',
                'from' => $o->getRepository()->getTable(),
                'where' => 'id = :id'
            ],
            [
                'id' => $o->getId()
            ]
        );
        if($shouldUpdate[0]->nb > 0)
            return $this->update($o);
        return $this->insert($o);
    }

    public function update(Model $o){
        if(count($o->getModifications()) > 0){
            $fields = implode(' AND ',
                array_map(
                    function(string $e):string {
                        return "{$e} = :{$e}";
                    },
                    $o->getModifications()
                )
            );
            $sql = 'UPDATE ' . ($o->getRepository()->getTable() ?? $this->table) . ' SET ' . $fields . ' WHERE id=' . $o->getId() .';';
            $query = $this->pdo->prepare($sql);
            return $query->execute(array_map(function($e) use ($o) { return $o->{"get" . ucfirst($e)}; }, $o->getModifications())) ? $this->getLastInsertId() : false;
        }
    }

    public function insert(Model $o){

        $fields = array_map(function($e){
            return $e['name'];
        }, $o->getSchema()['fields']);

        $f = [];
        $v = [];
        foreach ($fields as $field) {
            $value = $o->{"get" . ucfirst($field)};
            if(!is_array($value) && !is_object($value)){
                $f[] = $field;
                $v[$field] = $value;
            }
        }
        if(count($f) === 0)
            return false;
        $sql = 'INSERT INTO `' . ($o->getRepository()->getTable() ?? $this->table) . '` (`' . implode('`, `',$f) . '`) VALUES (:' . implode(', :',$f) . ');';
        $query = $this->pdo->prepare($sql);
        if($query->execute($v)){
            $o->setId($this->getLastInsertId());
            return true;
        }
        return false;
    }

}