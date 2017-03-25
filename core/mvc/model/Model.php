<?php

namespace Core\Mvc\Model;

use Core\Database\Database;
use PDO;


abstract class Model{

    protected $_pdo;
    protected $table;
    protected $entity = null;

    public function __construct()
    {
        $this->_pdo = Database::getInstance();
        $this->table = strtolower(str_replace('Model', '', substr(get_class($this), strrpos(get_class($this), '\\') + 1)));
        $entityClass = ucfirst($this->table);
        if(file_exists(ENTITY . $entityClass . 'Entity.php')){
            $this->entity = "\\App\\Entity\\{$entityClass}Entity";
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
        $entityName = ucfirst(strtolower($entity));
        $entityClass = $entityName . 'Entity.php';
        $entityNs = "App\\Entity\\{$entity}Entity";
        if(!file_exists(ENTITY . $entityClass))
            return false;
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
        $entityName = ucfirst(strtolower($entity));
        $entityClass = $entityName . 'Entity.php';
        $entityNs = "App\\Entity\\{$entity}Entity";
        if(!file_exists(ENTITY . $entityClass))
            return false;
        $query = $this->_pdo->prepare($sql);
        $query->execute($param);
        return $query->fetchAll(PDO::FETCH_CLASS, $entityNs);
    }



    public function getAll(){
        $sql = 'SELECT * FROM ' . $this->table . ';';
        return $this->fetchAll($sql);
    }

    public function get($field){
        $field = is_array($field) ? implode(',', $field) : $field;
        $sql = 'SELECT ' . $field . ' FROM ' . $this->table . ';';
        return $this->fetchAll($sql);
    }

    public function getById($id){
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE id = ?;';
        return $this->fetch($sql,[$id]);
    }

    public function update($o){
        if(count(array_keys($o->_modified)) > 0){
            $fields = implode(' AND ',
                array_map(
                    function($e){
                        return $e . ' = :' . $e;
                    },
                    array_keys($o->_modified)
                )
            );
            $sql = 'UPDATE ' . $this->table . ' SET ' . $fields . ' WHERE id=' . $o->id .';';
            $query = $this->_pdo->prepare($sql);
            return $query->execute($o->_modified) ? true : false;
        }
    }

    public function insert($o){
        $fields = get_class_vars(get_class($o));
        var_dump($fields);
        $f = [];
        $v = [];
        foreach ($fields as $field => $value) {
            if($field !== '_modified'){
                $value = $o->$field;
                if($value !== null){
                    $f[] = $field;
                    $v[$field] = $value;
                }
                elseif ($field == 'id'){
                    $f[] = $field;
                    $v[$field] = NULL;
                }
            }
        }
        if(count($f) === 0)
            return;
        $sql = 'INSERT INTO `' . $this->table . '` (`' . implode('`,` ',$f) . '`) VALUES (:' . implode(', :',$f) . ');';
        $query = $this->_pdo->prepare($sql);
        return $query->execute($v) ? true : false;
    }

}