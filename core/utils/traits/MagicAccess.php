<?php

namespace Core\Utils\Traits;

trait MagicAccess {

    use ContainerAccess, ArrayAccess;

    public function __set(string $key, $value){
        $this->set($key, $value);
    }

    public function __get(string $key){
        return $this->get($key);
    }

    public function __call(string $method, $param = []){
        if(preg_match('/^(get|set)$/',substr($method, 0, 3))){
            $m = lcfirst(substr($method, 3));
            switch (substr($method, 0, 3)){
                case 'get':
                    return $this->get($m);
                case 'set':
                    if(count($param))
                        return $this->set($m, $param[0]);
                    return $this->get($method);
            }
        }
        if(count($param)){
            return $this->set($method, $param[0]);
        }
        return $this->get($method);
    }

}