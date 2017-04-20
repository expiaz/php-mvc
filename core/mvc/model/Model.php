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

    public function __construct()
    {
        //TODO: replace locator to dependancy injections
        try{
            $this->schema = Schema::get($this)->schema();
        }
        catch (\Exception $e){
            $this->schema = null;
        }
    }

    /**
     * @param $args
     * @Deprecated
     */
    private function parseArgs($args){
        $props = null;
        if((is_object($args[0]) || (is_array($args[0]) && Helper::isAssociative($args[0]))) && count($args) === 1){
            //we assume that every property is passed throught this object
            $props = is_array($args[0]) ? $args[0] : (array) $args[0];
        }
        else{
            if(is_array($args[0]))
                $args = $args[0];

            $props = get_class_vars(
                get_class($this)
            );
            $i = 0;
            foreach ($props as $p => $v) {
                $props[$p] = $args[$i] ?? null;
                $i++;
            }
        }

        if(DEV){
            echo '[Model::create] ';
            echo '<br>props : ';
            print_r($props);
            echo '<br>values : ';
            print_r($args);
        }

        foreach ($props as $p => $v){
            if(!is_null($v) && $p{0} !== '_'){
                $func = 'set' . ucfirst(strtolower($p));
                $this->$func($v);
            }
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
        if(!in_array($k,array_keys(get_class_vars(get_called_class()))))
            return;

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