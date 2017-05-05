<?php

namespace Core\Factory;

use Core\utils\HttpParameterBag;
use Core\App\Loader;
use Core\Http\Route\Route;

class LoaderFactory extends Factory{

    public function create(Route $route, HttpParameterBag $parameters)
    {
        return new Loader($this->getContainer(), $route, $parameters);
    }

}