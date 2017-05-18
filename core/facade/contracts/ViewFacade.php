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

    static function render(string $viewPath, array $args = []){
        return (new View(static::getContainer(), $viewPath))->render($args);
    }
}