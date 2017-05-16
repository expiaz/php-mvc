<?php

namespace Core\Http;

use Core\App\Handler;
use Core\Http\Route\Route;

class Middleware extends RouterCallee {

    private $next;

    public function __construct($handler, $next = null)
    {
        parent::__construct($handler);
        if(! is_null($next)){
            $this->setNext($next);
        }
    }

    public function setNext($next){
        $this->next = function (Request $request, Response $response) use ($next){
            return $next->apply($request, $response);
        };
    }

    public function apply(... $args)
    {
        return parent::apply($args[0], $args[1], $this->next);
    }

}