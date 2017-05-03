<?php

namespace Core\Mvc\Repository;

use Core\Database\Orm\Schema\Table;
use Core\Mvc\Schema\Schema;
use Core\Database\Database;
use Core\Mvc\Model\Model;
use Core\Utils\DataContainer;


abstract class Repository{

    protected $database;
    protected $schema;
    protected $table;
    protected $model;

    public function __construct(Database $db, string $modelNs, Schema $schema)
    {
        $this->database = $db;
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
        return (new $this->model($this->schema));
    }

    private function hydrate(DataContainer $class){
        $model = new $this->model($this->schema);
        $schema = $model->getSchemaDefintion();
        foreach ($schema['fields'] as $field){
            if(property_exists($model, $field))
                $model->{"set" . ucfirst($field)}($class->{"get" . ucfirst($field)}());
        }
        return $model;
    }

    public function fetch(string $sql, array &$param = []){
        $upplet = $this->database->fetch($sql, $param);
        $model = $this->hydrate($upplet);
        return $model;
    }

    public function fetchAll(string $sql, array &$param = []){
        $upplets = $this->database->fetchAll($sql, $param);
        return array_map($upplets, function(DataContainer $e){
            return $this->hydrate($e);
        });
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
        $parameters = array($value);
        return $this->fetchAll($sql,$parameters);
    }

    public function getByFields(array $fields, array &$values){
        $where = implode(' AND ',array_map(function($e){
            return "{$e} = :{$e}";
        }, $fields));
        $sql = "SELECT * FROM {$this->table} WHERE {$where};";
        return $this->fetchAll($sql,$values);
    }

    public function getById($id){
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE id = ?;';
        $parameters = array($id);
        return $this->fetch($sql,$parameters);
    }


    public function persist(Model $o){
        if(is_null($o->getId())){
            return $this->update($o);
        }
        $shouldUpdate = $this->find(
            [
                'select' => ' COUNT(id) as nb',
                'from' => $o->getTable()->getName(),
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
            $sql = "UPDATE {$o->getTable()->getName()} SET {$fields} WHERE id={$o->getId()};";
            $parameters = array_map(function($e) use ($o) { return $o->{"get" . ucfirst($e)}; }, $o->getModifications());
            return $this->database->execute($sql, $parameters) ? $this->getLastInsertId() : false;
        }
    }

    public function insert(Model $o){

        $fields = array_map(function($e){
            return ":{$e}";
        }, $o->getSchemaDefintion()['fields']);

        $values = array_map(function($e) use ($o){
            return $o->{"get" . $e['name']};
        }, $o->getSchemaDefintion()['fields']);



        if(count($fields) === 0)
            return false;

        $sql = "INSERT INTO `{$o->getTable()->getName()}` (`" . implode('`, `', $fields) . "`) VALUES (" . implode(', ', $fields) . ")";

        if($this->database->execute($sql, $values)){
            $o->setId($this->getLastInsertId());
            return true;
        }
        return false;
    }

}