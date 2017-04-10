<?php

namespace Core;

use Core\Exception\FileNotFoundException;

class Autoloader{

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

        if(file_exists(ROOT . $fileName))
            require_once ROOT . $fileName;
        else
            throw new FileNotFoundException("{$fileName} does not exists");

    }

}