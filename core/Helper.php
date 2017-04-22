<?php
namespace Core;

final class Helper{

    /**
     * return the class name without the class type
     * e.g : IndexController becomes Index
     * @param $instance
     * @return string
     */
    public function getClassName($instance){

        if(is_object($instance))
            $instance = get_class($instance);

        if(strpos($instance, '//') !== false)
            return $this->normalizeName(preg_replace('/(Schema|Repository|Controller|Model)/i','',substr($instance, strrpos($instance, '\\') + 1)));
        else if(strpos($instance, DS) !== false)
            return $this->normalizeName(preg_replace('/(Schema|Repository|Controller|Model)/i','',substr($instance, strrpos($instance, DS) + 1)));

        return $instance;
    }


    /**
     * return the table name associated with the class
     * e.g : IndexController returns index
     * @param $instance
     * @return string
     */
    public function getTableName($instance){
        if(is_object($instance))
            $instance = get_class($instance);
        return strtolower( $this->getClassName($instance));
    }


    /**
     * returns the type of the class (Repo/Model/Controller)
     * e.g : IndexController returns Controller
     * @param $instance
     * @return string
     */
    public function getClassType($instance){
        if(is_object($instance))
            $instance = get_class($instance);

        $namespace = rtrim($instance, '\\');
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

    private function getX($instance, $class){
        if(is_object($instance))
            $instance = get_class($instance);
        $class = $this->normalizeName($class);
        if(strpos($instance, '\\'))
            $instance = $this->getClassName($instance);
        else
            $instance = $this->normalizeName($instance);
        return "App\\{$class}\\{$instance}{$class}";
    }

    public function getModelNs($instance){
        return $this->getX($instance, 'Model');
    }

    public function getSchemaNs($instance){
        return $this->getX($instance, 'Schema');
    }

    public function getControllerNs($instance){
        return $this->getX($instance, 'Controller');
    }

    public function getRepositoryNs($instance){
        return $this->getX($instance, 'Repository');
    }



    public function isValidNamespace($namespace){
        return preg_match("/^App[\\\](Model|Schema|Repository|Controller)[\\\]\w+$/",$namespace);
    }

}