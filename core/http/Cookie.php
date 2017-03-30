<?php

namespace Core\Http;

abstract class Cookie{

    public static function set($k, $v, $expire = null){
        /*
        if(isset($_COOKIE[$k])){
            echo "{$k} exists => {$_COOKIE[$k]}, setting it at {$v}<br/>";
            $_COOKIE[$k] = $v;
            return;
        }
        */
        if(is_null($expire))
            $expire = time() + 3600;
        setcookie($k,$v,$expire);
    }

    public static function get($k){
        return $_COOKIE[$k] ?? null;
    }

    public static function exists($k){
        return isset($_COOKIE[$k]);
    }

    public static function delete($k){
        if(isset($_COOKIE[$k])){
            unset($_COOKIE[$k]);
        }
    }

    public static function flush(){
        unset($_COOKIE);
    }

}