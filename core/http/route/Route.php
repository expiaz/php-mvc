<?php

namespace Core\Http\Route;

use Core\App;
use Core\Http\Middleware;
use Core\Http\Request;
use Core\Http\RequestScheme;
use Core\Http\Response;
use Core\Http\RouterCallee;
use Core\Utils\HttpParameterBag;
use Core\App\Handler;
use Core\Factory\LoaderFactory;

class Route extends RouterCallee {

    private $url;
    private $method;
    private $route;
    private $param;
    private $regex;

    private $middlewaresStack;
    private $middleware;

    public function __construct(string $route, $handler, string $method)
    {
        parent::__construct($handler);
        $this->route = $route;
        $this->method = $method;
        $this->middlewaresStack = [];
        $this->middleware = null;
        $this->makeRoute();
    }

    public function setMethod($method){
        $this->method = $method;
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

    public function getUrl(){
        return $this->url;
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

    public function use($middleware){
        /*
        $middlewareObject = new Middleware($middleware);

        if(! is_null($this->middleware)){
            $this->middleware->setNext($middlewareObject);
            $this->middlewaresStack[] = $this->middleware;
        }

        $middlewareObject->setNext($this);
        $this->middleware = $middlewareObject;
        */
        $middlewareObject = new Middleware($middleware);
        $this->middlewaresStack[] = $middlewareObject;

        return $this;
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

    public function &applyArguments(string $url){

        if(! is_null($url))
            $this->url = $url;

        $p = [];

        if($this->route == '*')
            return $p;

        if(preg_match($this->regex,$this->url,$parameters)){
            for($i = 0; $i < count($this->param); $i++){
                $p[$this->param[$i]] = $parameters[$i+1];
            }
        }

        return $p;
    }

    public function applyMiddlewares(Request $req, Response $rep){
        if(count($this->middlewaresStack) == 0){
            return $this->apply($req, $rep);
        }

        $prev = null;
        $first = null;
        foreach ($this->middlewaresStack as $middleware){
            if(is_null($prev)) $first = $middleware;
            else $prev->setNext($middleware);
            $prev = $middleware;
        }
        $prev->setNext($this);

        return $first->apply($req, $rep);
    }

}