<?php

namespace Core\Http;

use Core\App\Handler;
use Core\Helper;

class ControllerHandler extends Handler{

    private $type;

    private $controller;
    private $action;

    public function __construct(string $controller = 'index', string $action = 'index')
    {
        parent::__construct($controller . '@' . $action);
        $this->type = static::CONTROLLER;
        $this->controller = $controller;
        $this->action = $action;
    }

    public function getController(){
        return $this->controller;
    }

    public function getAction(){
        return $this->action;
    }

    public function trigger(... $args){

        $container = container();

        try{
            $controller = $container->resolve($container->get(Helper::class)->getControllerNs($this->controller));

            if(!method_exists($controller, $this->action)){
                throw new \Exception("{$this->action} does not exists in $this->controller");
            }
            $action = $this->action;
        }
        catch (\Exception $e){
            $controller = $container->resolve($container->get(Helper::class)->getControllerNs('index'));
            $action = 'error404';
        }

        return $controller->{$action}(... $args);
    }

}