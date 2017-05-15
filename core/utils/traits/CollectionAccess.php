<?php

namespace Core\Utils\Traits;

trait CollectionAccess{

    private $container;

    public function __construct()
    {
        $this->container = $this->initializeContainer();
    }

    public function initializeContainer(){
        return [];
    }

    public function beforeEach(string &$key){
        return true;
    }

    public function normalize(string &$key){

        if(!$this->beforeEach($key))
            return;

        if(is_array($key))
            $key = (object) $key;
        if(is_object($key))
            $key = get_class($key);
    }

    public function exists(string $key){

        if(!$this->beforeEach($key))
            return;

        $this->normalize($key);
        return isset($this->container[$key]);
    }

    public function set(string $key, $value){

        if(!$this->beforeEach($key))
            return;

        $this->normalize($key);

        $this->container[$key] = $value;
    }

    public function get(string $key, $default = null){

        if(!$this->beforeEach($key))
            return;

        $this->normalize($key);
        if($this->exists($key)){
            return $this->container[$key];
        }

        return $default;
    }

    public function unset(string $key){

        if(!$this->beforeEach($key))
            return;

        $this->normalize($key);
        if($this->exists($key)){
            unset($this->container[$key]);
        }
    }

    public function reset(){

        if(!$this->beforeEach($key))
            return;

        $this->container = $this->initializeContainer();
    }

    public function getAll(){
        return $this->container;
    }

}