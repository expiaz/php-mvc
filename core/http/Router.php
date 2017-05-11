<?php

namespace Core\Http;

use Core\Http\Route\Route;

final class Router{

    const ALL = 'ALL';
    const GET = 'GET';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const POST = 'POST';

    private $routes;
    private $default;

    public function __construct()
    {
        $this->routes = [
            static::GET => [],
            static::POST => [],
            static::PUT => [],
            static::DELETE => []
        ];

        $this->default = [
            static::GET => NULL,
            static::POST => NULL,
            static::PUT => NULL,
            static::DELETE => NULL,
            static::ALL => NULL
        ];

        //TODO add name (slug) and middleware
    }

    public function redirect(Url $url){
        header('Location:' . $url->build());
    }

    public function on(string $route, $handler){
        return $this->addRoute(static::ALL, $route, $handler);
    }

    public function get(string $route, $handler){
        return $this->addRoute(static::GET, $route, $handler);
    }

    public function post(string $route, $handler){
        return $this->addRoute(static::POST, $route, $handler);
    }

    public function put(string $route, $handler){
        return $this->addRoute(static::PUT, $route, $handler);
    }

    public function delete(string $route, $handler){
        return $this->addRoute(static::DELETE, $route, $handler);
    }


    private function addRoute(string $method, string $route, $handler){
        if($method === static::ALL || $route === '*'){
            return $this->default($method, $handler);
        }

        $route = $route === '/' ? $route : trim($route,'/');

        if($this->isDefined($route)){
            return;
        }

        $routeObject = new Route($route, $handler);

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
            $this->default[$method] = new Route('*', $handler);
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

        if($method === static::ALL){
            //default route for ALL $route->$method('*', ...)
            if($this->default[$method] instanceof Route){
                $this->default[$method]->apply($route);
                return true;
            }
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
        if($this->default[static::ALL] instanceof Route){
            $this->default[static::ALL]->apply($route);
            return true;
        }

        return false;
    }

    private function isDefined($route){
        foreach ($this->routes as $type => $routes) {
            foreach ($routes as $r){
                if($route === $r->getRoute()){
                    return true;
                }
            }
        }
        /*
        foreach ($this->default as $type => $r) {
            if($r instanceof Route){
                if($route === $r->getRoute()){
                    return true;
                }
            }
        }
        */
        return false;
    }

    private function isClosure($t) {
        return is_object($t);
    }

    private function resolveRequestMethod()
    {
        $methodName = $_SERVER['REQUEST_METHOD'];

        switch ($methodName){
            case static::GET:
                return static::GET;
            case static::POST:
                return static::POST;
            case static::PUT:
                return static::PUT;
            case static::DELETE:
                return static::DELETE;
            default:
                return static::ALL;
        }
    }

}