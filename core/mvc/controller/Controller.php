<?php
namespace Core\Mvc\Controller;

use Core\Cache;
use Core\Helper;

abstract class Controller{

    private $model = null;

    public function __construct()
    {
        $modelClass = Helper::getModelFilePathFromInstance($this);
        $modelNs = Helper::getModelNamespaceFromInstance($this);
        $model = Cache::get($modelNs);
        if(!is_object($model)){
            if(file_exists($modelClass)){
                $model = new $modelNs();
                Cache::set($modelNs, $model);
            }
            else
                $model = null;
        }
        $this->model = $model;
    }

    public function getModel($name = null){
        if($name){
            return Cache::get(Helper::getModelNamespaceFromName($name), true);
        }
        return $this->model;
    }

}