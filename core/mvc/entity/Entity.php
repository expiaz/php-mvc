<?php
namespace Core\Mvc\Entity;

use Core\Cache;
use Core\Helper;

abstract class Entity{

    public $id;
    public $_modified = [];
    private $_model = null;

    public function __construct($init_args = [])
    {
        $this->_model = Cache::get(Helper::getModelNamespaceFromInstance($this), true);

        if(count($init_args)){
            $this->parseArgs($init_args);
        }
    }

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
            echo '[Entity::create] ';
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
            echo '[Entity] __call ' . get_class($this) . ' '  . $function . ' ';
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

    public function setter($k,$v){
        if($v !== $this->$k){
            $this->_modified[$k] = $v;
        }
    }

}