<?php

namespace Core\Http;

use Core\Facade\Contracts\ResponseFacade;
use Core\Http\Route\Route;

final class Router{

    private $routes;
    private $default;

    public function __construct()
    {
        $this->routes = [
            Request::GET => [],
            Request::POST => [],
            Request::PUT => [],
            Request::DELETE => [],
            Request::ALL => []
        ];

        $this->default = [
            Request::GET => NULL,
            Request::POST => NULL,
            Request::PUT => NULL,
            Request::DELETE => NULL,
            Request::ALL => NULL
        ];

        //TODO add name (slug) and middleware
    }

    public function redirect(Url $url){
        ResponseFacade::withRedirect($url->build());
        //header('Location: ' . $url->build());
    }

    public function on(string $route, $handler){
        return $this->addRoute(Request::ALL, $route, $handler);
    }

    public function get(string $route, $handler){
        return $this->addRoute(Request::GET, $route, $handler);
    }

    public function post(string $route, $handler){
        return $this->addRoute(Request::POST, $route, $handler);
    }

    public function put(string $route, $handler){
        return $this->addRoute(Request::PUT, $route, $handler);
    }

    public function delete(string $route, $handler){
        return $this->addRoute(Request::DELETE, $route, $handler);
    }


    private function addRoute(string $method, string $route, $handler){

        if($route === '*'){
            return $this->default($method, $handler);
        }

        $route = $route === '/' ? $route : trim($route,'/');

        if($this->isDefined($route, $method)){
            return;
        }

        $routeObject = new Route($route, $handler, $method);

        $this->routes[$method][] = $routeObject;

        foreach ($this->routes as $type => $route){
            usort($this->routes[$type], function(Route $a, Route $b){
                return $b->getRegex() <=> $a->getRegex();
            });
        }

        return $routeObject;
    }

    private function default(string $method, $handler){
        if(!$this->default[$method] instanceof Route){
            $this->default[$method] = new Route('*', $handler, Request::ALL);
        }
        return $this->default[$method];
    }

    public function apply(string $route){


        $method = $this->resolveRequestMethod();

        if(empty($route)){
            $route = '/';
        }
        else{
            $route = trim($route, '/');
        }

        foreach ($this->routes[$method] as $r){
            if($r->match($route)){
                $r->apply();
                return true;
            }
        }

        //default route for ALL $route->$method('*', ...)
        if($this->default[$method] instanceof Route){
            $this->default[$method]->apply($route);
            return true;
        }

        //default route for ALL $route->on('*', ...) or $route->default()
        if($this->default[Request::ALL] instanceof Route){
            $this->default[Request::ALL]->apply($route);
            return true;
        }

        return false;
    }

    private function isDefined($route, $type){
        foreach ($this->routes[$type] as $r){
            if($route === $r->getRoute()){
                return true;
            }
        }

        return false;
    }

    private function isClosure($t) {
        return is_object($t);
    }

    private function resolveRequestMethod()
    {
        $methodName = $_SERVER['REQUEST_METHOD'];

        switch ($methodName){
            case Request::GET:
                return Request::GET;
            case Request::POST:
                return Request::POST;
            case Request::PUT:
                return Request::PUT;
            case Request::DELETE:
                return Request::DELETE;
            default:
                return Request::ALL;
        }
    }

}