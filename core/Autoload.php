<?php

namespace Core;

class Autoload{

    public static function register(){
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    public static function autoload($className){

        $className = ltrim($className, '\\');
        $fileName = '';
        $namespace = '';

        if($lastNsPos = strrpos($className, '\\')){
            $namespace = substr($className, 0, $lastNsPos);
            $namespace = strtolower($namespace);
            $className = substr($className, $lastNsPos + 1);
            $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }

        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        if(DEV)
            echo "{$fileName} autloaded <br>";

        require_once ROOT . $fileName;

    }

}