<?php
namespace Core\Mvc\Model;

use Core\Cache;
use Core\Database\Orm\Schema\Schema;
use Core\Helper;
use Core\Mvc\Repository\Repository;

abstract class Model{

    private $_modified = [];
    protected $id;
    protected $schema;

    public function __construct(Schema $schema)
    {
        //TODO: replace locator to dependency injections
        try{
            $this->schema = Schema::get($this)->schema();
        }
        catch (\Exception $e){
            $this->schema = null;
        }
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

    public function getSchema(): array{
        return $this->schema;
    }

    public function getModifications(): array{
        return $this->_modified;
    }

}