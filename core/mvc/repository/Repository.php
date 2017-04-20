<?php

namespace Core\Mvc\Repository;

use Core\Cache;
use Core\Database\Orm\Schema\Schema;
use Core\Exception\FileNotFoundException;
use Core\Helper;
use Core\Database\Database;
use Core\Mvc\Model\Model;
use PDO;


abstract class Repository{

    protected $pdo;
    protected $schema;
    protected $table;
    protected $model;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
        try{
            $this->schema = Schema::get($this)->schema();
            $this->table = $this->schema['table'];
        }
        catch (\Exception $e){
            $this->schema = null;
            $this->table = null;
        }
        $this->model = Cache::get(Helper::getModelNamespaceFromInstance($this), true);
    }

    public function getTable(){
        return $this->table;
    }

    public function getSchema(): array{
        return $this->schema;
    }

    public function getModel(): Model{
        if(is_null($this->model)){
            throw new \Exception(get_called_class() . "::getModel, model is not defined");
        }
        return (new $this->model());
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

    public function fetchInto($model, $sql, $param = []){
        $modelNs = '';
        if($model instanceof Model){
            $modelNs = $this->fetchIntoModel(get_class($model));
        }
        else if(Helper::isValidModelNamespace($model)){
           $modelNs = $model;
        }
        else{
            $modelClass = Helper::getModelFilePathFromName($model);
            $modelNs = Helper::getModelNamespaceFromName($model);
            if(!file_exists($modelClass))
                throw new FileNotFoundException("Model not found {$modelNs} at {$modelClass}");
        }

        $query = $this->pdo->prepare($sql);
        $query->execute($param);
        $query->setFetchMode(PDO::FETCH_CLASS, $modelNs);
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

    public function fetchAllInto($model, $sql, $param = []){
        $modelNs = '';
        if($model instanceof Model){
            $modelNs = $this->fetchIntoModel(get_class($model));
        }
        else if(Helper::isValidModelNamespace($model)){
            $modelNs = $model;
        }
        else{
            $modelClass = Helper::getModelFilePathFromName($model);
            $modelNs = Helper::getModelNamespaceFromName($model);
            if(!file_exists($modelClass))
                throw new FileNotFoundException("Model not found {$modelNs} at {$modelClass}");
        }

        $query = $this->pdo->prepare($sql);
        $query->execute($param);
        return $query->fetchAll(PDO::FETCH_CLASS, $modelNs);
    }

    public function getLastInsertId(){
        return $this->pdo->lastInsertId();
    }

    public function find($clause, $bind, $model = null){
        $sql = 'SELECT ' . ($clause['select'] ? is_array($clause['select']) ? implode(', ',$clause['select']) : $clause['select'] : '*') . ' ';
        $sql .= 'FROM ' . ($clause['from'] ? is_array($clause['from']) ? implode(', ', $clause['from']) : $clause['from'] : $this->table) . ' ';
        $sql .= $clause['where'] ? ( 'WHERE ' . (is_array($clause['where']) ? implode(' AND ', $clause['where']) : $clause['where'])) : '';
        $sql .= $clause['orderby'] ? ( 'ORDER BY ' . (is_array($clause['orderby']) ? implode(', ', $clause['orderby']) : $clause['orderby'])) : '';
        $sql .= $clause['groupby'] ? ( 'GROUP BY ' . (is_array($clause['groupby']) ? implode(', ', $clause['groupby']) : $clause['groupby'])) : '';
        $sql .= $clause['having'] ? ( 'HAVING ' . (is_array($clause['having']) ? implode(' AND ', $clause['having']) : $clause['having'])) : '';
        $sql .= $clause['limit'] ? ( 'LIMIT ' . (is_array($clause['limit']) ? implode(', ', $clause['limit']) : $clause['limit'])) : '';
        $sql.= ';';
        if($model)
            return $this->fetchAllInto($model, $sql, $bind);
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
        if(count(array_keys($o->getModifications())) > 0){
            $fields = implode(' AND ',
                array_map(
                    function($e){
                        return $e . ' = :' . $e;
                    },
                    array_keys($o->getModifications())
                )
            );
            $sql = 'UPDATE ' . ($o->getRepository()->getTable() ?? $this->table) . ' SET ' . $fields . ' WHERE id=' . $o->getId() .';';
            $query = $this->pdo->prepare($sql);
            return $query->execute(array_map(bind(function($e){ return $this->{"get" . ucfirst($e)}; }, $o), $o->getModifications())) ? $this->getLastInsertId() : false;
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
            return;
        $sql = 'INSERT INTO `' . ($o->getRepository()->getTable() ?? $this->table) . '` (`' . implode('`, `',$f) . '`) VALUES (:' . implode(', :',$f) . ');';
        $query = $this->pdo->prepare($sql);
        return $query->execute($v) ? $this->getLastInsertId() : false;
    }

}