<?php

namespace Core;

abstract class Cache{

    private static $_cached;

    public static function init(){
        self::$_cached = [];
    }

    public static function set($key,$value){
        self::$_cached[$key] = $value;
    }

    public static function get($key, $createInstanceAndReturnItIfNotSet = false){
        if(!isset(self::$_cached[$key])){
            if($createInstanceAndReturnItIfNotSet)
                self::$_cached[$key] = new $key();
            else
                return -1;
        }
        return self::$_cached[$key];
    }

}