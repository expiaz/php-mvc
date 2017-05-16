<?php

namespace Core\Facade;

use Core\App;

abstract class Facade{

    protected static $container = null;


    abstract static function getFacadeAccessor(): string;

    abstract static function getFacadedClass(): string;

    /*
    public static function create(){

        $args = func_get_args();

        if(count($args)){
            $ns = static::getFacadedClass();
            return new $ns(... $args);
        }

        try{
            return App::make(static::getFacadedClass());
        }
        catch (\Exception $e){
            $ns = static::getFacadedClass();
            return new $ns();
        }

    }
    */

    public static function resolveFacadedClass(){
        if(is_object($class = static::getFacadedClass())){
            return $class;
        }

        try{
            return App::make($class);
        }
        catch (\Exception $e){
            return null;
        }
    }

    public static function getContainer(string $service = null){
        if(static::$container === null){
            static::$container = container();
        }

        return $service !== null ? static::$container[$service] : static::$container;
    }

    public static function loadClass(array $args){

        if(count($args)){
            $ns = static::getFacadedClass();
            return new $ns(... $args);
        }

        try{
            return App::make(static::getFacadedClass());
        }
        catch (\Exception $e){
            $ns = static::getFacadedClass();
            return new $ns();
        }
    }

    public static function __callStatic(string $name, array $arguments)
    {

        $instance = static::resolveFacadedClass();

        if($instance === null){
            return static::loadClass($arguments);
        }

        if(! method_exists($instance, $name)){

            if($name === 'create'){
                return static::loadClass($arguments);
            }
            $klass = get_class($instance);
            throw new \Exception(get_called_class() . " {$klass} does not define a function named {$name}");
        }

        return $instance->{$name}(... $arguments);
    }

}