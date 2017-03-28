<?php
namespace Core\Mvc\Entity;

use Core\Cache;
use Core\Helper;

abstract class Entity{

    public $id;
    public $_modified = [];
    private $_model = null;
    public $_table;

    public function __construct($init_args = [])
    {

        $modelClass = Helper::getModelFilePathFromInstance($this);
        $modelNs = Helper::getModelNamespaceFromInstance($this);
        $this->_model = Cache::get($modelNs, true);
        $this->_table = Helper::getTableNameFromInstance($this);

        if(DEV)
            echo '[' . get_class($this) . '::__construct] modelFilePath = ' . $modelClass . ' & modelNs = ' . $modelNs . '<br>';

        /*if(!is_object($model)){
            if(file_exists($modelClass)){
                $model = new $modelNs();
                Cache::set($modelNs, $model);
            }
            else
                $model = null;
        }
        $this->_model = $model;
        */

        if(count($init_args)){
            $this->parseArgs($init_args);
        }

    }

    private function parseArgs($args){
        $props = null;
        if((is_object($args[0]) || (is_array($args[0]) && $this->isAssociative($args[0]))) && count($args) === 1){
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

    private function isAssociative(array $a){
        return count(array_filter(array_keys($a), 'is_string')) > 0;
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

    public function insert(){
        if(!is_null($this->_model)){
            $this->id = $this->_model->insert($this);
            return $this->id;
        }
        return false;
    }

    public function update(){
        if(!is_null($this->_model))
            return $this->_model->update($this);
        return false;
    }

}