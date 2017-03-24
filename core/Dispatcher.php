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

        }

        Query::setUrl($this->_url);
        Query::setController($controller);

        $this->load($controllerClass, $actionName, $param);

        //new $controllerClass(new $modelClass($tableName), $actionName, $param, $http);

    }

    private function load($controller, $action, $param){
        $controllerNs = "\\App\\Controller\\{$controller}";
        $controllerClass = new $controllerNs();
        if(!$this->methodExists($controllerClass, $action)){
            $param = array_merge([$action],$param);
            $action = 'index';
        }
        $http = [
            'GET' => array_slice($_GET,1),
            'POST' => $_POST
        ];
        Query::setAction($action);
        Query::setParam($param);
        Query::setHttp($http);
        $controllerClass->$action($param, $http);
    }

    private function methodExists($class, $method){
        $method = strtolower($method);
        $methods = array_map('strtolower', get_class_methods($class));
        if(in_array($method,$methods)){
            return true;
        }
        return false;
    }

}