<?php

namespace Core;

use Core\Exception\FileNotFoundException;

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
            if($createInstanceAndReturnItIfNotSet){
                try{
                    self::$_cached[$key] = new $key();
                }
                catch(FileNotFoundException $e){
                    return null;
                }
            }

            else
                return -1;
        }
        return self::$_cached[$key];
    }

}