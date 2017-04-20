<?php

namespace Core\Database\Orm;

use Core\Database\Database;
use Core\Database\Orm\Schema\Schema;
use Core\Exception\FileNotFoundException;
use Core\Helper;

final class ORM{

    private $schema;
    private $statement;
    private $table;

    public static function create($entity = null){
        if(! is_null($entity)){
            if(is_object($entity)){
                $instance = Helper::getClassNameFromInstance($entity);
            }
            else if(is_array($entity)){
                $ret = true;
                foreach ($entity as $e){
                    $ret = $ret && static::create($e);
                }
                return $ret;
            }
            else if(is_string($entity)){
                $instance = new static($entity);
            }
            else{
                $instance = "index";
            }
            return $instance->_create();
        }
        $ret = true;
        foreach (glob(\SCHEMA . "*Schema.php") as $filename) {
            $name = Helper::getClassNameFromFilePath($filename);
            $s = new static($name);
            $ret = $ret && $s->_create();
        }
        return $ret;
    }

    public static function schema($entity): array{

        if(! is_null($entity)){
            if(is_object($entity)){
                $instance =  Helper::getClassNameFromInstance($entity);
            }
            else if(is_array($entity)){
                $ret = [];
                foreach ($entity as $e){
                    $ret[] = static::schema($e);
                }
                return $ret;
            }
            else if(is_string($entity)){
                $instance = new static($entity);
            }
            else{
                $instance = "index";
            }
            return $instance->_schema();
        }
        $ret = [];
        foreach (glob(\SCHEMA . "*Schema.php") as $filename) {
            $name = Helper::getClassNameFromFilePath($filename);
            $s = new static($name);
            $ret = $ret[] = $s->_schema();
        }
        return $ret;
    }


    private function __construct($table)
    {
        try{
            $schema = Helper::getSchemaNamespaceFromName($table);
            new $schema();
            $this->table = Schema::getTable($table);
            $this->schema = $this->table->schema();
            $this->description = $this->table->statement();
        }
        catch(FileNotFoundException $e){
            $this->schema = [];
            $this->description = "";
        }
    }





    private function _create(){
        $pdo = Database::getInstance();
        $query = $pdo->prepare($this->statement);
        return $query->execute([]) ? true : false;
    }

    private function _schema(){
        return $this->schema;
    }

}