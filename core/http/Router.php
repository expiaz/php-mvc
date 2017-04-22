<?php

namespace Core\Http;

use Core\Http\Route\Route;

final class Router{

    private $routes;

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

    public function apply(array &$result, string $route){
        if(empty($route)){
            $route = '/';
        }
        else{
            $route = trim($route, '/');
        }

        $parameters = [];
        foreach ($this->routes as $r) {
            if($r->match($route, $parameters)){
                $result[0] = $r;
                $result[1] = $parameters;
                return true;
            }
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