<?php
namespace Core\Mvc\Controller;

use Core\Cache;
use Core\Helper;

abstract class Controller{

    private $repository = null;

    public function __construct()
    {
        $this->model = Cache::get(Helper::getRepositoryNamespaceFromInstance($this), true);
    }

    public function getRepository($name = null){
        if(! is_null($name)){
            if($name instanceof Model || $name instanceof Repository || $name instanceof Controller)
                $name = Helper::getClassNameFromInstance($name);
            elseif (Helper::isValidNamespace($name))
                $name = Helper::getClassNameFromNamespace($name);
            return Cache::get(Helper::getRepositoryNamespaceFromName($name), true);
        }
        return $this->model;
    }

}