<?php

namespace Core\Form;

class DataContainer{

    public function __call($name, $arguments)
    {
        $type = substr($name,0,3);
        $propName = strtolower(substr($name,3));
        switch($type){
            case 'set':
                $this->$propName = $arguments;
                break;
            case 'get':
                return $this->$propName;
                break;
        }
    }

    public function __get($prop){
        return $prop;
    }

    public function __set($prop, $val){
        $this->$prop = $val;
    }

}