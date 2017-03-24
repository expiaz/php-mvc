<?php
namespace Core\Mvc\Controller;

abstract class Controller{

    private $model = null;

    public function __construct()
    {
        $modelClass = ucfirst(strtolower(str_replace('Controller','',substr(get_class($this), strrpos(get_class($this),'\\') + 1)))) . 'Model';
        if(file_exists(MODEL . $modelClass . '.php')){
            $modelNs = "App\\Model\\{$modelClass}";
            $this->model = new $modelNs();
        }
    }

    public function getModel(){
        return $this->model;
    }

}