<?php
namespace Core\Mvc\Entity;

abstract class Entity{

    public $_modified = [];

    public function __call($function, $args){
        echo '[Entity] __call ' . get_class($this) . ' '  . $function . ' ';
        print_r($args);
        echo '<br>';
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

}