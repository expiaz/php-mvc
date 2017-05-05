<?php

namespace Core\App;

class Handler{

    private $controller;
    private $action;

    public function __construct(string $controller, string $action)
    {
        $this->controller = $controller;
        $this->action = $action;
    }

    public function getController(){
        return $this->controller;
    }

    public function getAction(){
        return $this->action;
    }

}