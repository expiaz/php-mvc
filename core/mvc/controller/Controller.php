<?php
namespace Core\Mvc\Controller;

use Core\Http\Query;

abstract class Controller{

    private $model = null;

    public function __construct()
    {
        $modelClass = ucfirst(strtolower(str_replace('Controller','',substr(get_class($this), strrpos(get_class($this),'\\') + 1)))) . 'Model';
        if(file_exists(MODEL . $modelClass . '.php')){
            $modelNs = "App\\Model\\{$modelClass}";
            $this->model = new $modelNs();
        }

        /*if(DEV){
            echo Controller::class . ' extended from ' . get_class($this). ' calling ' . $action . ' with ';
            print_r($param);
            echo '<br>';
            echo 'url : ' .  Query::getDisplayedUrl() . '<br>';
            echo 'query : ' . Query::getQueriedUrl() . '<br>';
            echo 'http : ' . Query::getHttpHeaders() . '<br>';
            echo '<br>';
        }*/


        //$this->$action($param,$http);
    }

    public static function load($action, $param, $http){

    }

    private function methodExists($method){
        $method = strtolower($method);
        $methods = array_map('strtolower', get_class_methods($this));
        if(in_array($method,$methods)){
            return true;
        }
        return false;
    }

    protected function getModel(){
        return $this->model;
    }

}