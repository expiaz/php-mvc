<?php

namespace Core\Http;

use ArrayAccess;
use Core\Utils\Interfaces\MagicAccess as MagicAccessInterface;
use Core\Utils\Traits\MagicAccess as MagicAccessTrait;

final class Cookie implements ArrayAccess, MagicAccessInterface {

    use MagicAccessTrait;

    public function &initializeContainer(){
        return $_COOKIE;
    }

    public function set(string $k, $v, $expire = null){
        if(is_null($expire))
            $expire = time() + 3600;
        setcookie((string) $k,(string) $v,$expire);
    }

    public function get(string $key, $default = null)
    {
        if(!$this->beforeEach($key))
            return;

        $this->normalize($key);
        if($this->exists($key)){
            return $_COOKIE[$key];
        }

        return $default;
    }

    public function exists(string $key)
    {
        if(!$this->beforeEach($key))
            return;

        $this->normalize($key);
        return isset($_COOKIE[$key]);
    }

    public function unset(string $key){

        if(!$this->beforeEach($key))
            return;

        $this->normalize($key);
        if($this->exists($key)){
            unset($_COOKIE[$key]);
        }
    }

    public function delete(string $k){
        $this->unset($k);
    }

    public  function flush(){
        $this->reset();
    }

}