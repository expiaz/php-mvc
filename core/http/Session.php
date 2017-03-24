<?php

namespace Core\Http;

abstract class Session{

    private static $_on = false;

    public static function activate(){
        if(self::$_on === true)
            return;

        self::$_on = true;
        session_start();
    }

    public static function destroy(){
        if(self::$_on === false)
            return;

        self::$_on = false;
        session_destroy();
    }

    public static function set($k,$v){
        if(self::$_on === false)
            return;

        $_SESSION[$k] = $v;
    }

    public static function get($k){
        if(self::$_on === false)
            return;

        return $_SESSION[$k] ?? null;
    }

    public static function delete($k){
        if(self::$_on === false)
            return;

        if(isset($_SESSION[$k])){
            unset($_SESSION[$k]);
        }
    }

    public static function flush(){
        if(self::$_on === false)
            return;

        self::destroy();
        self::activate();
    }

}