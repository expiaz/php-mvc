<?php
namespace Core\Mvc\Controller;

use Core\Cache;
use Core\Helper;
use Core\Mvc\Repository\Repository;

abstract class Controller{

    private $repository;

    public function __construct()
    {
        $this->repository = Cache::get(Helper::getRepositoryNamespaceFromInstance($this), true);
    }

    public function getRepository($name = null): Repository{
        if(! is_null($name)){
            if($name instanceof Model || $name instanceof Repository || $name instanceof Controller)
                $name = Helper::getClassNameFromInstance($name);
            elseif (Helper::isValidNamespace($name))
                $name = Helper::getClassNameFromNamespace($name);
            return Cache::get(Helper::getRepositoryNamespaceFromName($name), true);
        }
        return $this->repository;
    }

}