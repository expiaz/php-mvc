<?php

namespace Core\Mvc\Model;

use Core\Exception\FileNotFoundException;
use Core\Helper;
use Core\Database\Database;
use Core\Mvc\Entity\Entity;
use PDO;


abstract class Model{

    protected $_pdo;
    protected $table;
    protected $entity = null;

    public function __construct()
    {
        $this->_pdo = Database::getInstance();
        $this->table = Helper::getClassNameFromInstance($this);
        $entityClass = Helper::getEntityFilePathFromInstance($this);
        if(file_exists($entityClass)){
            $this->entity = Helper::getEntityNamespaceFromInstance($this);
        }
    }

    public function fetch($sql,$param = []){
        $query = $this->_pdo->prepare($sql);
        $query->execute($param);
        if($this->entity !== null){
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
            return $query->fetch();
        }
        return $query->fetch(PDO::FETCH_OBJ);
    }

    public function fetchInto($entity, $sql, $param = []){
        $entityClass = Helper::getEntityFilePathFromName($entity);
        $entityNs = Helper::getEntityNamespaceFromName($entity);
        if(!file_exists($entityClass))
            throw new FileNotFoundException("Entity not found {$entityNs} at {$entityClass}");
        $query = $this->_pdo->prepare($sql);
        $query->execute($param);
        $query->setFetchMode(PDO::FETCH_CLASS, $entityNs);
        return $query->fetch();
    }

    public function fetchAll($sql,$param = []){
        $query = $this->_pdo->prepare($sql);
        $query->execute($param);
        if($this->entity !== null){
            return $query->fetchAll(PDO::FETCH_CLASS, $this->entity);
        }
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function fetchAllInto($entity, $sql, $param = []){
        $entityClass = Helper::getEntityFilePathFromName($entity);
        $entityNs = Helper::getEntityNamespaceFromName($entity);
        if(!file_exists($entityClass))
            throw new FileNotFoundException("Entity not found {$entityNs} at {$entityClass}");
        $query = $this->_pdo->prepare($sql);
        $query->execute($param);
        return $query->fetchAll(PDO::FETCH_CLASS, $entityNs);
    }

    public function getLastInsertId(){
        return $this->_pdo->lastInsertId();
    }

    public function find($clause, $bind, $entity = null){
        $sql = 'SELECT ' . ($clause['select'] ? is_array($clause['select']) ? implode(', ',$clause['select']) : $clause['select'] : '*') . ' ';
        $sql .= 'FROM ' . ($clause['from'] ? is_array($clause['from']) ? implode(', ', $clause['from']) : $clause['from'] : $this->table) . ' ';
        $sql .= $clause['where'] ? ( 'WHERE ' . (is_array($clause['where']) ? implode(' AND ', $clause['where']) : $clause['where'])) : '';
        $sql .= $clause['orderby'] ? ( 'ORDER BY ' . (is_array($clause['orderby']) ? implode(', ', $clause['orderby']) : $clause['orderby'])) : '';
        $sql .= $clause['groupby'] ? ( 'GROUP BY ' . (is_array($clause['groupby']) ? implode(', ', $clause['groupby']) : $clause['groupby'])) : '';
        $sql .= $clause['having'] ? ( 'HAVING ' . (is_array($clause['having']) ? implode(' AND ', $clause['having']) : $clause['having'])) : '';
        $sql .= $clause['limit'] ? ( 'LIMIT ' . (is_array($clause['limit']) ? implode(', ', $clause['limit']) : $clause['limit'])) : '';
        $sql.= ';';
        if($entity)
            return $this->fetchAllInto($entity, $sql, $bind);
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

    public function update(Entity $o){
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
            $query = $this->_pdo->prepare($sql);
            return $query->execute($o->_modified) ? $this->getLastInsertId() : false;
        }
    }

    public function insert(Entity $o){
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
        $query = $this->_pdo->prepare($sql);
        return $query->execute($v) ? $this->getLastInsertId() : false;
    }

}