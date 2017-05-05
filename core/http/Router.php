<?php

namespace Core\Http;

use Core\Http\Route\Route;

final class Router{

    const GET = 'GET';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const POST = 'POST';

    private $routes;
    private $default;

    public function __construct()
    {
        $this->routes = [];
    }

    public function redirect(Url $url){
        header('Location:' . $url->build());
    }

    public function on($route, $handler){

        $route = $route === '/' ? $route : trim($route,'/');

        if($this->isDefined($route)){
            return;
        }

        $this->routes[] = new Route($route, $handler);

        usort($this->routes, function(Route $a, Route $b){
            return $b->getRegex() <=> $a->getRegex();
        });

    }

    public function default($handler){
        if(!$this->default instanceof Route){
            $this->default = new Route('*', $handler);
        }
    }

    public function apply(string $route){

        if(empty($route)){
            $route = '/';
        }
        else{
            $route = trim($route, '/');
        }

        foreach ($this->routes as $r) {
            if($r->match($route)){
                $r->apply();
                return true;
            }
        }
        if($this->default instanceof Route){
            $this->default->apply($route);
            return true;
        }
        return false;
    }

    private function isDefined($route){
        foreach ($this->routes as $r) {
            if($route === $r->getRoute()){
                return true;
            }
        }
        return false;
    }

    private function isClosure($t) {
        return is_object($t);
    }

}