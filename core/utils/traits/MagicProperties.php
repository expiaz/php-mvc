<?php

namespace Core\Utils\Traits;

trait MagicProperties {

    public function __set(string $key, $value){
        $this->{$key} = $value;
        //$this->set($key, $value);
    }

    public function __get(string $key){
        //echo "UppletContainer::__get($key) : {$this->{$key}}\n";
        return $this->{$key};
    }

    public function __call(string $method, $param = []){
        if(preg_match('/^(get|set)$/',substr($method, 0, 3))){
            $m = lcfirst(substr($method, 3));
            switch (substr($method, 0, 3)){
                case 'get':
                    return $this->{$m};
                case 'set':
                    if(count($param)){
                        //$this->set($m, $param[0]);
                        return $this->{$m} = $param[0];
                    }
                    break;
            }
        }
        if(count($param)){
            //$this->set($method, $param[0]);
            return $this->{$method} =  $param[0];
        }
        return $this->{$method};
    }

    public function __invoke($parameter)
    {
        return $this->{$parameter};
    }

}