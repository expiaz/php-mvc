<?php

namespace Core\Http\Route;

use Core\Exception\FileNotFoundException;

class Route{

    private $type;
    private $route;
    private $param;
    private $regex;
    private $handler;

    const CLOSURE = 1;
    const CONTROLLER = 2;

    public function __construct(string $route, mixed $handler)
    {
        $this->route = $route;
        $this->handler = $this->handle($handler);
        $this->makeRoute();
    }

    public function getType(){
        return $this->type;
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

    private function handle(mixed &$handler): array{

        if($handler instanceof Closure){
            $this->handler = $handler;
            $this->type = static::CLOSURE;
            return;
        }

        if(is_array($handler)){
            $this->type = static::CONTROLLER;
            $this->handler = [$handler['controller'] ?? 'index', $handler['action'] ?? 'index'];
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
        $this->handler = [
            'controller' => $controller,
            'action' => $action
        ];

    }

    private function makeRoute(){
        preg_match_all('/:(\w*)/',$this->route,$p,PREG_SET_ORDER);
        $this->param = array_map(function($e){
            return $e[1];
        }, $p);
        $regex = str_replace('/','\/',$this->route);
        $this->regex = '|^' . preg_replace('/:([^\/]*)/','([^\/]*)',$regex) . '$|';
    }

    public function match(string $route, array &$p = []){
        if(preg_match($this->regex,$route,$parameters)){
            for($i = 0; $i < count($this->param); $i++){
                $p[$this->param[$i]] = $parameters[$i+1];
            }
            return true;
        }
        $p = [];
        return false;
    }

    public function trigger(array &$parameters){
        switch ($this->type){
            case static::CLOSURE:
                call_user_func_array($this->handler, $parameters);
                return true;
            case static::CONTROLLER:
                try{
                    container()->resolve("App\\Controller\\{$this->handler['controller']}Controller")->{$this->handler['action']}(... $parameters);
                }
                catch(FileNotFoundException $e){
                    return false;
                }
                return true;
        }
    }

}