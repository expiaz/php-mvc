<?php

namespace Core\Facade;

use Core\Autoloader;
use Core\Exception\FileNotFoundException;

class AliasesLoader{


    public static $aliases = [];

    private static $registered = false;

    public static function getAliases(): array{
        return static::$aliases;
    }

    private $resolvedAliases = [];

    public function __construct(array $aliases = [])
    {

        if(static::$registered === false){
            spl_autoload_register([$this, 'load'], true, true);
            //spl_autoload_register([$this, 'load'], true);
            static::$aliases = require_once CORE . 'aliases.php';
            static::$registered = true;
        }

        /*
        $aliases = count($aliases) ? $aliases : static::getAliases();

        if(count($aliases))
            $this->load($aliases);
        else
            $this->load(static::getAliases());
        */
    }

    public function load(string $alias){

        $alias = trim($alias, '\\');
        if(strpos($alias, '\\') !== false){
            substr($alias, strrpos($alias, '\\') + 1);
        }

        if(! isset(static::getAliases()[$alias])){
            // throw new FileNotFoundException("AliasesLoader::load {$alias} does not exists");
            return false;
        }

        if(! isset($this->resolvedAliases[$alias])){
            $this->resolvedAliases[$alias] = static::getAliases()[$alias];
            class_alias($this->resolvedAliases[$alias], $alias);
            return class_exists($this->resolvedAliases[$alias]) ? true : Autoloader::autoload($this->resolvedAliases[$alias]);
        }
        //already loaded
        return true;


    }

}