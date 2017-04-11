<?php

namespace Core\Mvc\Repository;

use Core\Exception\FileNotFoundException;
use Core\Helper;
use Core\Database\Database;
use Core\Mvc\Model\Model;
use PDO;


abstract class Repository{

    protected $pdo;
    protected $table;
    protected $model = null;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
        $this->table = Helper::getTableNameFromInstance($this);
        if(file_exists(Helper::getModelFilePathFromInstance($this))){
            $this->model = Helper::getModelNamespaceFromInstance($this);
        }
        else{
            $this->model = null;
        }
    }

    public function fetch($sql,$param = []){
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

    public function fetchAll($sql,$param = []){
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
            return $o->insert();
        }
        $shouldUpdate = $this->find(
            [
                'select' => ' COUNT(id) as nb',
                'from' => $o->_table,
                'where' => 'id = :id'
            ],
            [
                'id' => $o->getId()
            ]
        );
        if($shouldUpdate[0]->nb > 0)
            return $o->update();
        return $o->insert();
    }

    public function update(Model $o){
        if(count(array_keys($o->_modified)) > 0){
            $fields = implode(' AND ',
                array_map(
                    function($e){
                        return $e . ' = :' . $e;
                    },
                    array_keys($o->_modified)
                )
            );
            $sql = 'UPDATE ' . ($o->_table ?? $this->table) . ' SET ' . $fields . ' WHERE id=' . $o->id .';';
            $query = $this->pdo->prepare($sql);
            return $query->execute($o->_modified) ? $this->getLastInsertId() : false;
        }
    }

    public function insert(Model $o){
        if(DEV){
            echo '[Model::insert] ';
            var_dump($o);
        }

        /*$fields = array_values(array_filter(array_keys(get_class_vars(get_class($o))),function($e){
            return $e{0} !== '_';
        }));
        echo '<br>fields : ';
        var_dump($fields);*/

        $fields = array_map(function($e){
            return $e['name'];
        }, $o->_schema);

        if(DEV) {
            echo '<br>fields : ';
            var_dump($fields);
        }
        $f = [];
        $v = [];
        foreach ($fields as $field) {
            $value = $o->$field;
            IF(DEV)
                echo '<br>field => value : ' . $field . ' => ' . $value;
            if(!is_array($value) && !is_object($value)){
                $f[] = $field;
                $v[$field] = $value;
            }
        }
        if(count($f) === 0)
            return;
        $sql = 'INSERT INTO `' . ($o->_table ?? $this->table) . '` (`' . implode('`, `',$f) . '`) VALUES (:' . implode(', :',$f) . ');';
        if(DEV) {
            echo '<br>sql : ';
            var_dump($sql);
            echo '<br>values : ';
            var_dump($v);
            echo '<br/>';
        }
        $query = $this->pdo->prepare($sql);
        return $query->execute($v) ? $this->getLastInsertId() : false;
    }

}