<?php
namespace Core\Mvc\Entity;

use Core\Cache;
use Core\Helper;

abstract class Entity{

    public $_modified = [];
    private $_model = null;

    public function __construct()
    {
        $modelClass = Helper::getModelFilePathFromInstance($this);
        $modelNs = Helper::getModelNamespaceFromInstance($this);
        if(DEV)
            echo '[' . get_class($this) . '] modelFilePah = ' . $modelClass . ' & modelNs = ' . $modelNs . '<br>';
        $model = Cache::get($modelNs);
        if(!is_object($model)){
            if(file_exists($modelClass)){
                $model = new $modelNs();
                Cache::set($modelNs, $model);
            }
            else
                $model = null;
        }
        $this->_model = $model;
    }

    public function getModel(){
        return $this->_model;
    }

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