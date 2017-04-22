<?php

namespace Core\Http;

use ArrayAccess;

final class Cookie implements ArrayAccess {

    public function set(string $k, $v, $expire = null){
        if(is_null($expire))
            $expire = time() + 3600;
        setcookie((string) $k,(string) $v,$expire);
    }

    public function get(string $k){
        return $_COOKIE[$k] ?? null;
    }

    public function exists(string $k){
        return isset($_COOKIE[$k]);
    }

    public function delete(string $k){
        if($this->exists($k)){
            unset($_COOKIE[$k]);
        }
    }

    public  function flush(){
        unset($_COOKIE);
    }

    public function __set($key, $value){
        $this->set($key, $value);
    }

    public function __get($key = null){
        return $this->get($key);
    }

    public function __call($method, $param = []){
        if(preg_match('/^(get|set)$/',substr($method, 0, 3))){
            $m = lcfirst(substr($method, 3));
            switch (substr($method, 0, 3)){
                case 'get':
                    return $this->get($m);
                case 'set':
                    if(count($param))
                        return $this->set($m, $param[0]);
                    return;
            }
        }
        if(count($param)){
            return $this->set($method, $param[0]);
        }
        return $this->get($method);
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