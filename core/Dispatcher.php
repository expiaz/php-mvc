<?php
namespace Core;

use Core\Http\Query;
use Core\Http\Router;

class Dispatcher{

    private $_url;
    private $_request;

    public function __construct($url){
        $this->_url = $url;
        $this->parse();
    }

    private function parse(){
        $this->_request = explode('/',$this->_url);
        $this->filterRequest();
        $this->dispatch();
    }

    private function filterRequest(){

        $req = array_filter($this->_request, function ($e){
            return $e != "";
        });

        $this->_request = [
            'controller' => $req[0] ?? 'index',
            'action' => $req[1] ?? 'index',
            'param' => array_slice($req,2)
        ];

    }

    private function dispatch(){
        $loaders = Router::apply($this->_url);
        switch($loaders['result']){
            case 'CLOSURE':
                return;
            case 'ROUTE':
                $this->find($loaders['controller'],$loaders['action'],$loaders['param']);
                break;
            case 'NO_ROUTE':
            default:
                $this->find();
        }


    }

    private function find($ctrl = null, $act = null, $p = null){
        $controller = $ctrl ?? $this->_request['controller'];
        $action = $act ?? $this->_request['action'];
        $param = $p ?? $this->_request['param'];
        $tableName = strtolower($controller);

        $controllerClass = ucfirst(strtolower($controller)) . 'Controller';
        $actionName = $action;
        $fileName = CONTROLLER . $controllerClass . '.php';

        if(!file_exists($fileName)){

            //controller was the requested action
            $actionName = strtolower($controller);

            // index controller
            $controllerClass = 'IndexController';
            $controller = 'index';

            //trunc parameters
            if($action !== 'index'){
                $param = count($param) > 0
                    ? array_merge([$action],$param)
                    : [$action];
            }

            //change require
            //$fileName = CONTROLLER . $controllerClass . '.php';
        }

        Query::setUrl($this->_url);
        Query::setController($controller);

        $modelClass = ucfirst(strtolower($controller)) . 'Model';
        $modelFileName = MODEL . $modelClass . '.php';
        if(!file_exists($modelFileName)){
            $modelClass = 'IndexModel';
            //$modelFileName = MODEL . $modelClass . '.php';
        }

        $http = [
            'GET' => array_slice($_GET,1),
            'POST' => $_POST
        ];

        $controllerClass = "\\App\\Controller\\{$controllerClass}";
        $modelClass = "\\App\\Model\\{$modelClass}";

        new $controllerClass(new $modelClass($tableName), $actionName, $param, $http);

    }

    private function load($controller, $action){

    }

}