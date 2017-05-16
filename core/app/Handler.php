<?php

namespace Core\App;

class Handler{

    const CONTROLLER = 1;
    const CLOSURE = 2;

    protected $callable;

    public function __construct($ToHandle)
    {
        $this->callable = $ToHandle;
    }

    public function getHandled(){
        return $this->callable;
    }

    public function trigger(... $args){
        return call_user_func_array($args);
    }

}