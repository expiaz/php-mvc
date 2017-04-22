<?php
namespace Core;

use Core\Exception\FileNotFoundException;
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
        $defined = [];
        $loaded = $this->container[Router::class]->apply($defined, $this->url);
        if($loaded){
            switch ($defined[0]->getType()){
                case Route::CLOSURE:
                    call_user_func_array($defined[1]->getHandler(), $defined[1]);
                    break;
                case Route::CONTROLLER:
                    try{
                        $controller = $this->container->resolve($this->container->get(Helper::class)->getControllerNs($defined[0]->getHandler()[0]));
                    }
                    catch (FileNotFoundException $e){
                        $controller = $this->container->resolve($this->container->get(Helper::class)->getControllerNs('index'));
                    }
                    try{
                        $controller->{$defined[0]->getHandler()[1]}(... $defined[1]);
                    }
                    catch(\Exception $e){
                        try{
                            $controller->index(... $defined[1]);
                        }
                        catch (\Exception $e){
                            $controller->index();
                        }
                    }
                    break;
            }
        }
        else{
            $controller = $this->container->resolve($this->container->get(Helper::class)->getControllerNs('index'));
            $controller->index();
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