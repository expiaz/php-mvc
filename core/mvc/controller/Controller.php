<?php
namespace Core\Mvc\Controller;

use Core\Cache;
use Core\Helper;

abstract class Controller{

    private $model = null;

    public function __construct()
    {
        $this->model = Cache::get(Helper::getModelNamespaceFromInstance($this), true);
    }

    public function getModel($name = null){
        if($name){
            if(is_object($name))
                $name = Helper::getClassNameFromInstance($name);
            elseif (is_string($name) && preg_match('/(Entity)|(Model)|(Controller)/',$name))
                $name = Helper::getClassNameFromNamespace($name);
            return Cache::get(Helper::getModelNamespaceFromName($name), true);
        }
        return $this->model;
    }

}