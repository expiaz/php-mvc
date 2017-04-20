<?php
namespace Core;

abstract class Helper{

    /**
     * return the class name without the class type
     * e.g : IndexController becomes Index
     * @param $instance
     * @return string
     */
    public static function getClassNameFromInstance($instance){
        return static::getClassNameFromNamespace(get_class($instance));
    }

    public static function getClassNameFromNamespace($namespace){
        $instanceClass = substr($namespace, strrpos($namespace, '\\') + 1);
        $v =  static::normalizeName(str_replace('Schema','',str_replace('Repository','',str_replace('Model','',str_replace('Controller','',$instanceClass)))));
        return $v;
    }

    public static function getClassNameFromFilePath($path){
        $instanceClass =  substr($path,strrpos($path, DS) + 1);
        return static::normalizeName(str_replace('Schema','',str_replace('Repository','',str_replace('Model','',str_replace('Controller','',$instanceClass)))));
    }


    /**
     * return the table name associated with the class
     * e.g : IndexController returns index
     * @param $instance
     * @return string
     */
    public static function getTableNameFromInstance($instance){
        return static::getTableNameFromNamespace(get_class($instance));
    }

    public static function getTableNameFromNamespace($namespace){
        return strtolower(static::getClassNameFromNamespace($namespace));
    }


    /**
     * returns the type of the class (Repo/Model/Controller)
     * e.g : IndexController returns Controller
     * @param $instance
     * @return string
     */
    public static function getClassTypeFromInstance($instance){
        return static::getClassTypeFromNamespace(get_class($instance));
    }

    public static function getClassTypeFromNamespace($namespace){
        $namespace = rtrim($namespace, '\\');
        $pos = strrpos($namespace, '\\');
        if($pos && strlen($namespace) - 1 >= $pos)
            $name = substr($namespace, strrpos($namespace, '\\') + 1);
        else
            return $namespace;
        if(strpos($name, 'Model') !== false){
            return 'Model';
        }
        if(strpos($name, 'Controller') !== false){
            return 'Controller';
        }
        if(strpos($name, 'Repository') !== false){
            return 'Repository';
        }
        if(strpos($name, 'Schema') !== false){
            return 'Schema';
        }
    }

    /**
     * return the namespace of an instance
     * e.g instance of App\Controller\IndexController returns App\Controller
     * @param $instance
     * @return bool|string
     */
    public static function getNamespaceFromInstance($instance){
        $instanceNs = get_class($instance);
        return substr($instanceNs, 0, strrpos($instanceNs, '\\'));
    }

    /**
     * MyAweSomeClaSs becomes Myawesomeclass
     * @param $name
     * @return string
     */
    public static function normalizeName($name){
        return ucfirst(strtolower($name));
    }

    /**
     * is an array associative ? (["key" => "value"])
     * @param array $a
     * @return bool
     */
    public static function isAssociative(array $a){
        return count(array_filter(array_keys($a), 'is_string')) > 0;
    }



    public static function getModelNamespaceFromInstance($instance){
        $name = static::getClassNameFromInstance($instance);
        $model = "App\\Model\\{$name}Model";
        return $model;
    }

    public static function getModelFilePathFromInstance($instance){
        $name = static::getClassNameFromInstance($instance);
        $model = MODEL . "{$name}Model.php";
        return $model;
    }
    

    public static function getControllerNamespaceFromInstance($instance){
        $name = static::getClassNameFromInstance($instance);
        $model = "App\\Controller\\{$name}Controller";
        return $model;
    }

    public static function getControllerFilePathFromInstance($instance){
        $name = static::getClassNameFromInstance($instance);
        $model = CONTROLLER . "{$name}Controller.php";
        return $model;
    }
    

    public static function getRepositoryNamespaceFromInstance($instance){
        $name = static::getClassNameFromInstance($instance);
        $model = "App\\Repository\\{$name}Repository";
        return $model;
    }

    public static function getRepositoryFilePathFromInstance($instance){
        $name = static::getClassNameFromInstance($instance);
        $model = REPOSITORY . "{$name}Repository.php";
        return $model;
    }
    
    

    public static function getModelNamespaceFromName($name){
        $name = static::normalizeName($name);
        $model = "App\\Model\\{$name}Model";
        return $model;
    }

    public static function getModelFilePathFromName($name){
        $name = static::normalizeName($name);
        $model = MODEL . "{$name}Model.php";
        return $model;
    }
    

    public static function getControllerNamespaceFromName($name){
        $name = static::normalizeName($name);
        $model = "App\\Controller\\{$name}Controller";
        return $model;
    }

    public static function getControllerFilePathFromName($name){
        $name = static::normalizeName($name);
        $model = CONTROLLER . "{$name}Controller.php";
        return $model;
    }
    

    public static function getRepositoryNamespaceFromName($name){
        $name = static::normalizeName($name);
        $model = "App\\Repository\\{$name}Repository";
        return $model;
    }

    public static function getRepositoryFilePathFromName($name){
        $name = static::normalizeName($name);
        $model = REPOSITORY . "{$name}Repository.php";
        return $model;
    }


    public static function getSchemaNamespaceFromName($name){
        $name = static::normalizeName($name);
        $schema = "App\\Model\\Schema\\{$name}Schema";
        return $schema;
    }
    

    public static function isValidModelNamespace($namespace){
        return static::isValidNamespace($namespace, 'Model');
    }

    public static function isValidControllerNamespace($namespace){
        return static::isValidNamespaceForClass($namespace, 'Controller');
    }

    public static function isValidRepositoryNamespace($namespace){
        return static::isValidNamespaceForClass($namespace, 'Repository');
    }

    public static function isValidSchemaNamespace($namespace){
        return static::isValidNamespaceForClass($namespace, 'Schema');
    }

    public static function isValidNamespaceForClass($namespace, $class){
        return static::getClassTypeFromNamespace($namespace) === $class;
    }

    public static function isValidNamespace($namespace){

        $v =  preg_match("/^App[\\\](Model|Schema|Repository|Controller)[\\\]\w+$/",$namespace);
        //echo "Helper::isValidNamespace {$namespace}" . ($v ? " y" : " n") . "\n<br>";
        return $v;
        return static::isValidRepositoryNamespace($namespace) || static::isValidControllerNamespace($namespace) || static::isValidModelNamespace($namespace) || static::isValidSchemaNamespace($namespace);
    }

}