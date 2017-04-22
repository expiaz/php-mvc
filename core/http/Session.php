<?php

namespace Core\Http;

use ArrayAccess;

final class Session implements ArrayAccess {

    private $on;

    public function __construct()
    {
        $this->on = false;
    }

    public function activate(){
        if($this->on === true)
            return;

        $this->on = true;
        session_start();
    }

    public function destroy(){
        if($this->on === false)
            return;

        $this->on = false;
        session_destroy();
    }

    public function set($k,$v){
        if($this->on === false)
            return;

        $_SESSION[$k] = $v;
    }

    public function get($k){
        if($this->on === false)
            return;

        return $this->exists($k) ? $_SESSION[$k] : null;
    }

    public function exists($k){
        return isset($_SESSION[$k]);
    }

    public function delete($k){
        if($this->on === false)
            return;

        if($this->exists($k)){
            unset($_SESSION[$k]);
        }
    }

    public function flush(){
        if($this->on === false)
            return;

        $this->destroy();
        $this->activate();
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