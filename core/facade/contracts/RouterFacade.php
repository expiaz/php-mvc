<?php

namespace Core\Facade\Contracts;

use Core\Facade\Facade;
use Core\Http\Router;

class RouterFacade extends Facade {


    static function getFacadeAccessor(): string
    {
        return 'Router';
    }

    static function getFacadedClass(): string
    {
        return Router::class;
    }
}