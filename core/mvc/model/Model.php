<?php
namespace Core\Mvc\Model;

use Core\Database\Orm\Schema\Table;
use Core\Mvc\Repository\Repository;
use Core\Mvc\Schema\Schema;

abstract class Model{

    private $_modified = [];
    protected $id;
    protected $schema;
    protected $repository;
    protected $table;

    public function __construct(Repository $r)
    {
        //TODO: replace locator to dependency injections
        $this->schema = $r->getSchema();
        $this->table = $r->getTable();
        $this->repository = $r;
    }

    public function __call($function, $args){
        if(DEV){
            echo '[Model] __call ' . get_class($this) . ' '  . $function . ' ';
            print_r($args);
            echo '<br>';
        }

        $type = substr($function,0,3);
        $propName = strtolower(substr($function,3));
        $props = array_map(
            function($e) {
                return strtolower($e);
            },
            array_keys(get_class_vars(get_class($this)))
        );
        if(in_array($propName,$props)){
            switch($type){
                case 'get':
                    return $this->$propName;
                    break;
                case 'set':
                    if($args[0] !== $this->$propName){
                        $this->$propName = $args[0];
                        $this->_modified[$propName] = $args[0];
                    }
                    break;
            }
        }
    }

    protected function setter($k, $v){
        /*if(!in_array($k,array_keys(get_class_vars(get_called_class()))))
            return;*/

        if($v !== $this->{$k}){
            $this->_modified[] = $k;
        }
    }

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        return $this->id = $id;
    }

    public function getSchema(): Schema{
        return $this->schema;
    }

    public function getTable(): Table{
        return $this->table;
    }

    public function getSchemaDefintion(): array{
        return $this->schema->schema();
    }

    public function getModifications(): array{
        return $this->_modified;
    }

}