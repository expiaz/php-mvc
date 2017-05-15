<?php

namespace Core\Http\Route;

use Core\Utils\HttpParameterBag;
use Core\App\Handler;
use Core\Factory\LoaderFactory;

class Route{

    private $type;
    private $method;
    private $route;
    private $param;
    private $regex;
    private $handler;

    private $url;

    const CLOSURE = 1;
    const CONTROLLER = 2;

    public function __construct(string $route, $handler, string $method)
    {
        $this->route = $route;
        $this->method = $method;
        $this->handle($handler);
        $this->makeRoute();
    }

    public function getType(){
        return $this->type;
    }

    public function getMethod(){
        return $this->method;
    }

    public function getRoute(){
        return $this->route;
    }

    public function getRegex(){
        return $this->regex;
    }

    public function getHandler(){
        return $this->handler;
    }

    private function handle(&$handler){

        if(is_callable($handler)){
            $this->handler = $handler;
            $this->type = static::CLOSURE;
            return;
        }

        if(is_array($handler)){
            $this->type = static::CONTROLLER;
            $this->handler = new Handler($handler['controller'] ?? 'index', $handler['action'] ?? 'index');
            return;
        }

        $i = strpos($handler, '@');
        if($i === -1){
            $controller = $handler;
            $action = 'index';
        }
        else{
            $controller = substr($handler,0,$i);
            $action = substr($handler,$i+1);
        }

        $this->type = static::CONTROLLER;
        $this->handler = new Handler($controller, $action);

    }

    private function makeRoute(){
        if($this->route == '*'){
            $this->regex = '|^(.*)$|';
            return;
        }

        preg_match_all('/:(\w*)/',$this->route,$p,PREG_SET_ORDER);
        $this->param = array_map(function($e){
            return $e[1];
        }, $p);
        $regex = str_replace('/','\/',$this->route);
        $this->regex = '|^' . preg_replace('/:([^\/]*)/','([^\/]*)',$regex) . '$|';
    }

    public function match(string $route){

        if($this->route == '*'){
            return true;
        }

        if(preg_match($this->regex,$route)){
            $this->url = $route;
            return true;
        }
        return false;

    }

    public function apply(string $url = null){

        if(!is_null($url))
            $this->url = $url;

        if($this->route == '*')
            return container(LoaderFactory::class)->create($this, new HttpParameterBag(explode('/',$this->url)));

        $p = [];
        if(preg_match($this->regex,$this->url,$parameters)){
            for($i = 0; $i < count($this->param); $i++){
                $p[$this->param[$i]] = $parameters[$i+1];
            }
        }

        container(LoaderFactory::class)->create($this, new HttpParameterBag($p));
    }

}