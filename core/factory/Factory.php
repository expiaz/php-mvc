<?php

namespace Core\Factory;

use Core\App\Container;

abstract class Factory{

    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    protected function getContainer(){
        return $this->container;
    }

}