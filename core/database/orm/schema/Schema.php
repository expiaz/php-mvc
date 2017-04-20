<?php

namespace Core\Database\Orm\Schema;

use Closure;
use Core\Exception\FileNotFoundException;
use Core\Helper;

class Schema{

    private static $namespaceByClassName = [];
    private static $tableNameByNamespace = [];
    private static $tableInstanceByName = [];

    public static function _create($instance, string $tableName = null, Closure $callback = null){
        if(is_object($instance)){
            $instance = get_class($instance);
        }
        if(is_null($tableName)){
            $tableName = strtolower(Helper::getClassNameFromNamespace($instance));
        }
        $table = new Table($tableName);
        static::$namespaceByClassName[strtolower(Helper::getClassNameFromNamespace($instance))] = $instance;
        static::$tableNameByNamespace[$instance] = $tableName;
        static::$tableInstanceByName[$tableName] = $table;
        if(! is_null($callback)){
            return call_user_func($callback, $table);
        }
    }

    public static function _get($name): Table{

        $tableName = null;

        if(is_object($name)){
            $name = get_class($name);
        }

        if(in_array($name, array_keys(static::$tableInstanceByName))){
            return static::$tableInstanceByName[$name];
        }
        elseif(in_array($name, array_keys(static::$tableNameByNamespace))){
            $tableName = static::$tableNameByNamespace[$name];
        }
        elseif(in_array(strtolower($name), array_keys(static::$namespaceByClassName))){
            $tableName = static::$tableNameByNamespace[static::$namespaceByClassName[strtolower($name)]] ?? null;
        }
        if(! is_null($tableName) && in_array($tableName, array_keys(static::$tableInstanceByName))){
            return static::$tableInstanceByName[$tableName];
        }

        try{
            new $name();
            return static::get($name);
        }
        catch (FileNotFoundException $e){
            try{
                $classNs = Helper::getSchemaNamespaceFromName($name);
                new $classNs();
                return static::get($classNs);
            }
            catch (FileNotFoundException $e){
                throw new \Exception("Schema::get {$name} does not exists");
            }
        }
    }

    public static function get($entityNs): Table{
        if(is_object($entityNs))
            $entityNs = get_class($entityNs);
        if(is_array($entityNs))
            $entityNs = get_class((object) $entityNs);

        if(Helper::isValidNamespace($entityNs)){
            $ns = Helper::getSchemaNamespaceFromName(Helper::getClassNameFromNamespace($entityNs));;
            return (new $ns())->schema();
        }
        throw new \Exception("Schema::get {$entityNs} schema not found");
    }

}