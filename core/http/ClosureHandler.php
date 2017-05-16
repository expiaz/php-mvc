<?php

namespace Core\Http;

use Core\App\Handler;

class ClosureHandler extends Handler{

    private $type;

    public function __construct(Closure $handler)
    {
        $this->type = static::CLOSURE;
        $this->callable = $handler;
    }

    public function getHandled(){
        return $this->callable;
    }

    public function trigger(... $args){
        return call_user_func_array($args);
    }

}