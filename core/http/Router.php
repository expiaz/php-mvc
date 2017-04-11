<?php

namespace Core\Http;

abstract class Router{

    private static $_routes = [];

    public static function redirect($args){
        if(!is_array($args))
            return;
        header('Location:' . Query::build($args['controller'] ?? null,$args['action'] ?? null,$args['param'] ?? null,$args['get'] ?? null));
    }

    public static function on($route, $handler){

        $route = $route === '/' ? $route : trim($route,'/');

        if(static::isDefined($route)){
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

        if(static::isClosure($handler)){
            $routeEntry['type'] = 'CLOSURE';
            $routeEntry['handler'] = $handler;
            static::$_routes[] = $routeEntry;

            return;
        }

        if(is_array($handler)) {
            $routeEntry['type'] = 'CONTROLLER';
            $routeEntry['handler'] = $handler;
            static::$_routes[] = $routeEntry;

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

        static::$_routes[] = $routeEntry;

        usort(static::$_routes, function($a,$b){
            return $b['regex'] <=> $a['regex'];
        });

    }

    public static function apply($route){
        if(empty($route)){
            $route = '/';
        }
        else{
            $route = trim($route, '/');
        }

        foreach (static::$_routes as $r){
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

    private static function isDefined($route){
        foreach (static::$_routes as $r) {
            if($route === $r['route']){
                return true;
            }
        }
        return false;
    }

    private static function isClosure($t) {
        return is_object($t);
    }

}