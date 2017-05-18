<?php

namespace Core\Http;

use Core\Facade\Contracts\ResponseFacade;
use Core\Http\Route\Route;

final class Router{

    private $routes;
    private $defaults;

    private $middlewareForAll;

    public function __construct()
    {
        $this->routes = [
            Request::GET => [],
            Request::POST => [],
            Request::PUT => [],
            Request::DELETE => [],
            Request::ALL => []
        ];
        $this->middlewareForAll = null;
        $this->defaults = [
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
    }

    public function use($handler){
        $this->middlewareForAll = $handler;
        foreach ($this->routes as $routes){
            foreach ($routes as $route) {
                $route->use($handler);
            }
        }
        foreach ($this->defaults as $defaultRoute){
            if(! is_null($defaultRoute)){
                $defaultRoute->use($handler);
            }
        }
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


    private function addRoute(string $method, string $route, $handler):Route{

        if($route === '*'){
            return $this->default($method, $handler);
        }

        $route = $route === '/' ? $route : trim($route,'/');

        if($alreadyDefined = $this->isDefined($route, $method)){
            return $alreadyDefined;
        }

        $routeObject = new Route($route, $handler, $method);

        $this->routes[$method][] = $routeObject;

        usort($this->routes[$method], function(Route $a, Route $b){
            return $b->getRegex() <=> $a->getRegex();
        });

        if(! is_null($this->middlewareForAll)){
            $routeObject->use($this->middlewareForAll);
        }

        return $routeObject;
    }

    private function default(string $method, $handler){
        if(!$this->defaults[$method] instanceof Route){
            $this->defaults[$method] = new Route('*', $handler, Request::ALL);
        }
        return $this->defaults[$method];
    }

    public function apply(string $route, $requestMethod): Route{

        $method = $this->resolveRequestMethod($requestMethod);

        if(empty($route)){
            $route = '/';
        }
        else{
            $route = trim($route, '/');
        }

        foreach ($this->routes[$method] as $r){
            if($r->match($route)){
                return $r;
            }
        }

        //routes for Request::ALL => $router->on('route', ....);
        foreach ($this->routes[Request::ALL] as $r){
            if($r->match($route)){
                $r->setMethod($requestMethod);
                return $r;
            }
        }

        //default route for ALL $router->$method('*', ...)
        if($this->defaults[$method] instanceof Route){
            return $this->defaults[$method];
        }

        //default route for ALL $router->on('*', ...) or $route->defaults(...)
        if($this->defaults[Request::ALL] instanceof Route){
            $this->defaults[Request::ALL]->setMethod($requestMethod);
            return $this->defaults[Request::ALL];
        }

        $defaultRoute = new Route('/', 'index@error404', $requestMethod);
        if(! is_null($this->middlewareForAll)){
            $defaultRoute->use($this->middlewareForAll);
        }

        return $defaultRoute;
    }

    private function isDefined($route, $type){
        foreach ($this->routes[$type] as $r){
            if($route === $r->getRoute()){
                return $r;
            }
        }

        return false;
    }

    private function resolveRequestMethod($method)
    {
        $methodName = $method;

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