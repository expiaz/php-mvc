<?php

namespace Core\Mvc\Model;

use Core\Database\Database;
use PDO;


abstract class Model{

    private $_pdo;
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
            $query->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, $this->entity);
            return $query->fetch();
        }
        return $query->fetch(PDO::FETCH_OBJ);
    }

    public function fetchAll($sql,$param = []){
        $query = $this->_pdo->prepare($sql);
        $query->execute($param);
        if($this->entity !== null){
            return $query->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, $this->entity);
        }
        return $query->fetchAll(PDO::FETCH_OBJ);
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

}