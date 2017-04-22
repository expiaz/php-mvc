<?php
namespace Core;

final class Helper{

    /**
     * return the class name without the class type
     * e.g : IndexController becomes Index
     * @param $instance
     * @return string
     */
    public function getClassNameFromInstance($instance){
        return $this->getClassNameFromNamespace(get_class($instance));
    }

    public function getClassNameFromNamespace($namespace){
        $instanceClass = substr($namespace, strrpos($namespace, '\\') + 1);
        $v =  $this->normalizeName(str_replace('Schema','',str_replace('Repository','',str_replace('Model','',str_replace('Controller','',$instanceClass)))));
        return $v;
    }

    public function getClassNameFromFilePath($path){
        $instanceClass =  substr($path,strrpos($path, DS) + 1);
        return $this->normalizeName(str_replace('Schema','',str_replace('Repository','',str_replace('Model','',str_replace('Controller','',$instanceClass)))));
    }


    /**
     * return the table name associated with the class
     * e.g : IndexController returns index
     * @param $instance
     * @return string
     */
    public function getTableNameFromInstance($instance){
        return $this->getTableNameFromNamespace(get_class($instance));
    }

    public function getTableNameFromNamespace($namespace){
        return strtolower( $this->getClassNameFromNamespace($namespace));
    }


    /**
     * returns the type of the class (Repo/Model/Controller)
     * e.g : IndexController returns Controller
     * @param $instance
     * @return string
     */
    public function getClassTypeFromInstance($instance){
        return $this->getClassTypeFromNamespace(get_class($instance));
    }

    public function getClassTypeFromNamespace($namespace){
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
    public function getNamespaceFromInstance($instance){
        $instanceNs = get_class($instance);
        return substr($instanceNs, 0, strrpos($instanceNs, '\\'));
    }

    /**
     * MyAweSomeClaSs becomes Myawesomeclass
     * @param $name
     * @return string
     */
    public function normalizeName($name){
        return ucfirst(strtolower($name));
    }

    /**
     * is an array associative ? (["key" => "value"])
     * @param array $a
     * @return bool
     */
    public function isAssociative(array $a){
        return count(array_filter(array_keys($a), 'is_string')) > 0;
    }



    public function getModelNamespaceFromInstance($instance){
        $name = $this->getClassNameFromInstance($instance);
        $model = "App\\Model\\{$name}Model";
        return $model;
    }

    public function getModelFilePathFromInstance($instance){
        $name = $this->getClassNameFromInstance($instance);
        $model = MODEL . "{$name}Model.php";
        return $model;
    }
    

    public function getControllerNamespaceFromInstance($instance){
        $name = $this->getClassNameFromInstance($instance);
        $model = "App\\Controller\\{$name}Controller";
        return $model;
    }

    public function getControllerFilePathFromInstance($instance){
        $name = $this->getClassNameFromInstance($instance);
        $model = CONTROLLER . "{$name}Controller.php";
        return $model;
    }
    

    public function getRepositoryNamespaceFromInstance($instance){
        $name = $this->getClassNameFromInstance($instance);
        $model = "App\\Repository\\{$name}Repository";
        return $model;
    }

    public function getRepositoryFilePathFromInstance($instance){
        $name = $this->getClassNameFromInstance($instance);
        $model = REPOSITORY . "{$name}Repository.php";
        return $model;
    }
    
    

    public function getModelNamespaceFromName($name){
        $name = $this->normalizeName($name);
        $model = "App\\Model\\{$name}Model";
        return $model;
    }

    public function getModelFilePathFromName($name){
        $name = $this->normalizeName($name);
        $model = MODEL . "{$name}Model.php";
        return $model;
    }
    

    public function getControllerNamespaceFromName($name){
        $name = $this->normalizeName($name);
        $model = "App\\Controller\\{$name}Controller";
        return $model;
    }

    public function getControllerFilePathFromName($name){
        $name = $this->normalizeName($name);
        $model = CONTROLLER . "{$name}Controller.php";
        return $model;
    }
    

    public function getRepositoryNamespaceFromName($name){
        $name = $this->normalizeName($name);
        $model = "App\\Repository\\{$name}Repository";
        return $model;
    }

    public function getRepositoryFilePathFromName($name){
        $name = $this->normalizeName($name);
        $model = REPOSITORY . "{$name}Repository.php";
        return $model;
    }


    public function getSchemaNamespaceFromName($name){
        $name = $this->normalizeName($name);
        $schema = "App\\Model\\Schema\\{$name}Schema";
        return $schema;
    }
    

    public function isValidModelNamespace($namespace){
        return $this->isValidNamespaceForClass($namespace, 'Model');
    }

    public function isValidControllerNamespace($namespace){
        return $this->isValidNamespaceForClass($namespace, 'Controller');
    }

    public function isValidRepositoryNamespace($namespace){
        return $this->isValidNamespaceForClass($namespace, 'Repository');
    }

    public function isValidSchemaNamespace($namespace){
        return $this->isValidNamespaceForClass($namespace, 'Schema');
    }

    public function isValidNamespaceForClass($namespace, $class){
        return $this->getClassTypeFromNamespace($namespace) === $class;
    }

    public function isValidNamespace($namespace){

        $v =  preg_match("/^App[\\\](Model|Schema|Repository|Controller)[\\\]\w+$/",$namespace);
        //echo "Helper $this->isValidNamespace {$namespace}" . ($v ? " y" : " n") . "\n<br>";
        return $v;
        return $this->isValidRepositoryNamespace($namespace) || $this->isValidControllerNamespace($namespace) || $this->isValidModelNamespace($namespace) || $this->isValidSchemaNamespace($namespace);
    }

}