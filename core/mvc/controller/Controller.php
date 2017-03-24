<?php
namespace Core\Mvc\Controller;

use Core\Http\Query;

abstract class Controller{

    private $model;

    public function __construct($model, $action, $param, $http)
    {
        $this->model = $model;
        if($this->methodExists($action) === false){
            $param = array_merge([$action],$param);
            $action = 'index';
        }

        Query::setAction($action);
        Query::setParam($param);
        Query::setHttp($http);

        if(DEV){
            echo Controller::class . ' extended from ' . get_class($this). ' calling ' . $action . ' with ';
            print_r($param);
            echo '<br>';
            echo 'url : ' .  Query::getDisplayedUrl() . '<br>';
            echo 'query : ' . Query::getQueriedUrl() . '<br>';
            echo 'http : ' . Query::getHttpHeaders() . '<br>';
            echo '<br>';
        }


        $this->$action($param,$http);
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