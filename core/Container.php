<?php

namespace Core;

use Closure;

class Container implements ArrayAccess {

    private $container;

    public function __construct()
    {
        $this->container = [];
        $this->container['container'] = $this;
    }

    public function singleton(Closure $singleton){
        return call_user_func($singleton, $this);
    }

    public function factory(Closure $factory){

    }

    public function set($key, $value){
        $this->container[(string) $key] = $value;
    }

    public function get($key, ... $p){
        if(isset($this->container[$key])){
            $value = $this->container[$key];
            if($value instanceof Closure)
                return call_user_func_array($value, array_merge([$this], $p));
            return $value;
        }
        throw new Exception("Out of bound");
    }

    public function delete($key){
        if($this->exists($key))
            unset($this->container[$key]);
    }

    public function exists($key){
        return isset($this->container[$key]);
    }

    public function __set($key, $value){
        $this->set($key, $value);
    }

    public function __get($key = null){
        return $this->get($key);
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            return;
        }
        $this->set($offset, $value);
    }

    public function offsetExists($offset) {
        return $this->exists($offset);
    }

    public function offsetUnset($offset) {
        $this->delete($offset);
    }

    public function offsetGet($offset) {
        return $this->get($offset);
    }

}