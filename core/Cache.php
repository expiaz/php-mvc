<?php

namespace Core;

use Core\Exception\FileNotFoundException;

abstract class Cache{

    private static $_cached;

    public static function init(){
        static::$_cached = [];
    }

    public static function set($key,$value){
        static::$_cached[$key] = $value;
    }

    public static function get($key, $createInstanceAndReturnItIfNotSet = false){
        if(is_object($key))
            $key = get_class($key);
        if(!isset(static::$_cached[$key])){
            if($createInstanceAndReturnItIfNotSet){
                try{
                    static::$_cached[$key] = new $key();
                }
                catch(FileNotFoundException $e){
                    return null;
                }
            }

            else
                return -1;
        }
        return static::$_cached[$key];
    }

}