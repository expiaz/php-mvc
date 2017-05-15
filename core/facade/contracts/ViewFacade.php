<?php

namespace Core\Facade\Contracts;

use Core\Facade\Facade;
use Core\Mvc\View\View;

class ViewFacade extends Facade {


    static function getFacadeAccessor(): string
    {
        return 'View';
    }

    static function getFacadedClass(): string
    {
        return View::class;
    }

    static function create(string $viewPath, array $args = []){
        return static::getContainer(static::getFacadedClass())->render($viewPath, $args);
    }
}