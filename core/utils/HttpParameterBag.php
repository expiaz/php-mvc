<?php

namespace Core\Utils;

class HttpParameterBag implements \ArrayAccess {

    protected $parameters;

    public function __construct(array $baseParameters = [])
    {
        $this->parameters = [];
        foreach ($baseParameters as $k => $v)
            $this->add($k, $v);
    }

    public function getBag(){
        return $this->parameters;
    }

    public function add($key, $parameter){
        $this->parameters[$key] = $this->escape($parameter);
    }

    private function escape($p){
        return is_array($p) ? array_map(array(__CLASS__, 'escape'), $p) : htmlspecialchars($p);
    }

    public function exists($key){
        return isset($this->parameters[$key]);
    }

    public function get($key){
        if(!$this->exists($key))
            return null;

        return $this->parameters[$key];
    }

    public function remove($key){
        if(!$this->exists($key))
            return null;

        unset($this->parameters[$key]);
    }

    public function offsetExists($offset)
    {
        return $this->exists($offset);
    }


    public function offsetGet($offset)
    {
        return $this->get($offset);
    }


    public function offsetSet($offset, $value)
    {
        return $this->add($offset, $value);
    }


    public function offsetUnset($offset)
    {
        return $this->remove($offset);
    }

    public function __get($key){
        return $this->get($key);
    }

    public function __set($key, $v){
        return $this->add($key, $v);
    }

    public function __call($method, $param = []){
        if(preg_match('/^(get|set)$/',substr($method, 0, 3))){
            $m = lcfirst(substr($method, 3));
            switch (substr($method, 0, 3)){
                case 'get':
                    return $this->get($m);
                case 'set':
                    if(count($param))
                        return $this->add($m, $param[0]);
                    return;
            }
        }
        if(count($param)){
            return $this->add($method, $param[0]);
        }
        return $this->get($method);
    }

}