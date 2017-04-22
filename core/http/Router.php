<?php

namespace Core\Http;

final class Router{

    private $routes;

    public function __construct()
    {
        $this->routes = [];
    }

    public static function redirect(Url $url){
        header('Location:' . $url->build());
    }

    public function on($route, $handler){

        $route = $route === '/' ? $route : trim($route,'/');

        if($this->isDefined($route)){
            return;
        }

        $routeEntry = [];

        preg_match_all('/:(\w*)/',$route,$p,PREG_SET_ORDER);
        $param = array_map(function($e){
            return $e[1];
        }, $p);
        $regex = str_replace('/','\/',$route);
        $regex = '|^' . preg_replace('/:([^\/]*)/','([^\/]*)',$regex) . '$|';

        $routeEntry['route'] = $route;
        $routeEntry['param'] = $param;
        $routeEntry['regex'] = $regex;

        if($this->isClosure($handler)){
            $routeEntry['type'] = 'CLOSURE';
            $routeEntry['handler'] = $handler;
            $this->routes[] = $routeEntry;

            return;
        }

        if(is_array($handler)) {
            $routeEntry['type'] = 'CONTROLLER';
            $routeEntry['handler'] = $handler;
            $this->routes[] = $routeEntry;

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

        $routeEntry['type'] = 'CONTROLLER';
        $routeEntry['handler'] = [
            'controller' => $controller,
            'action' => $action
        ];

        $this->routes[] = $routeEntry;

        usort($this->routes, function($a,$b){
            return $b['regex'] <=> $a['regex'];
        });

    }

    public function apply($route){
        if(empty($route)){
            $route = '/';
        }
        else{
            $route = trim($route, '/');
        }

        foreach ($this->routes as $r){
            if(DEV){
                echo '[Router::apply] preg_match(' . $r['regex'] . ', ' . $route . ') <br>';
            }

            if(preg_match($r['regex'],$route,$parameters)){
                $params = [];
                for($i = 0; $i < count($r['param']); $i++){
                    $params[$r['param'][$i]] = $parameters[$i+1];
                }
                switch($r['type']){
                    case 'CLOSURE':
                        $r['handler']($params);
                        return [
                            'result' => 'CLOSURE'
                        ];
                    case 'CONTROLLER':
                        return [
                            'result' => 'ROUTE',
                            'controller' => $r['handler']['controller'],
                            'action' => $r['handler']['action'],
                            'param' => $params
                        ];
                        return $ret;
                }
            }
        }
        return [
            'result' => 'NO_ROUTE'
        ];
    }

    private function isDefined($route){
        foreach ($this->routes as $r) {
            if($route === $r['route']){
                return true;
            }
        }
        return false;
    }

    private function isClosure($t) {
        return is_object($t);
    }

}