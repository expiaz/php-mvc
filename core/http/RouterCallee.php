<?php

namespace Core\Http;

use Core\App\Handler;

class RouterCallee{

    protected $type;
    protected $handler;

    const CLOSURE = 1;
    const CONTROLLER = 2;

    public function __construct($handler)
    {
        $this->handler = $this->handle($handler);
    }

    protected function handle(&$handler){
        if(is_callable($handler)){
            return new ClosureHandler($handler);
        }

        if(is_array($handler)){
            return new ControllerHandler($handler['controller'], $handler['action']);
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

        return new ControllerHandler($controller, $action);
    }

    public function getType(){
        return $this->type;
    }

    public function getHandler(): Handler{
        return $this->handler;
    }

    public function apply(... $args){
        return $this->handler->trigger(... $args);
    }


}