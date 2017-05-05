<?php

namespace Core\Utils;

class ParameterBag {

    protected $parameters;

    public function __construct(... $baseParameters)
    {
        $this->parameters = array_map(function($e){
            return $this->add($e);
        }, $baseParameters);
    }

    public function getBag(){
        return $this->parameters;
    }

    public function add($p){
        $this->parameters[] = $p;
    }

    public function isEmpty(){
        return count($this->parameters) === 0;
    }

}