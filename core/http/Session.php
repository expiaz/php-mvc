<?php

namespace Core\Http;

abstract class Session{

    private static $_on = false;

    public static function activate(){
        if(static::$_on === true)
            return;

        static::$_on = true;
        session_start();
    }

    public static function destroy(){
        if(static::$_on === false)
            return;

        static::$_on = false;
        session_destroy();
    }

    public static function set($k,$v){
        if(static::$_on === false)
            return;

        $_SESSION[$k] = $v;
    }

    public static function get($k){
        if(static::$_on === false)
            return;

        return $_SESSION[$k] ?? null;
    }

    public static function exists($k){
        return isset($_SESSION[$k]);
    }

    public static function delete($k){
        if(static::$_on === false)
            return;

        if(isset($_SESSION[$k])){
            unset($_SESSION[$k]);
        }
    }

    public static function flush(){
        if(static::$_on === false)
            return;

        static::destroy();
        static::activate();
    }

}