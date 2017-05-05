<?php
namespace Core\App;

use Core\Utils\HttpParameterBag;
use Core\Factory\LoaderFactory;
use Core\Http\Query;
use Core\Http\Request;
use Core\Http\Route\Route;
use Core\Http\Router;

final class Dispatcher{

    private $url;
    private $request;
    private $container;

    public function __construct(Container $container, string $url){
        $this->url = $url;
        $this->container = $container;
        $this->parse();
    }

    private function parse(){
        $this->request = explode('/',$this->url);
        $this->filterRequest();
        $this->dispatch();
    }

    private function filterRequest(){

        $req = array_filter($this->request, function ($e){
            return $e != "";
        });

        $this->request = [
            'controller' => $req[0] ?? 'index',
            'action' => $req[1] ?? 'index',
            'param' => array_slice($req,2)
        ];

    }

    private function dispatch(){

        if($this->container[Router::class]->apply($this->url) === false){
            $this->container[LoaderFactory::class]->create(new Route('/', 'index@error404'), new HttpParameterBag());
        }

    }

    private function find($ctrl = null, $act = null, $p = null){

        $controller = $ctrl ?? $this->_request['controller'];
        $action = $act ?? $this->_request['action'];
        $param = $p ?? $this->_request['param'];

        $fileName = Helper::getControllerFilePathFromName($controller);
        if(!file_exists($fileName)){

            // index controller
            $controller = 'index';

            //trunc parameters
            if($action !== 'index'){
                $param = count($param) > 0
                    ? array_merge([$action],$param)
                    : [$action];
            }

            //controller was the requested action
            $action = strtolower($controller);
        }

        $this->load($controller, $action, $param);
    }


    private function load($controller, $action, $param){
        $controllerNs = Helper::getControllerNamespaceFromName($controller);
        $controllerClass = new $controllerNs();

        if(!$this->methodExists($controllerClass, $action)){
            $param = array_merge([$action],$param);
            $action = 'index';
        }
        $request = new Request(array_slice($_GET,1),$_POST, $_FILES);

        Cache::set($controllerNs, $controllerClass);
        Query::setAction($action);
        Query::setParam($param);
        Query::setRequest($request);
        Query::setUrl($this->_url);
        Query::setController($controllerNs);

        if(DEV) {
            echo "[Dispatcher::load] controller = {$controller}, action = {$action}, param = ";
            print_r($param);
        }
        //$controllerClass->$action($param, $http);
        call_user_func_array([$controllerClass,$action],array_merge([$request],$param));
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