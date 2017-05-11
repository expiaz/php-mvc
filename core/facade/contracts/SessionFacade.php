<?php

namespace Core\Facade\Contracts;

use Core\Facade\Facade;
use Core\Http\Session;

class SessionFacade extends Facade {


    static function getFacadeAccessor(): string
    {
        return 'Session';
    }

    static function getFacadedClass(): string
    {
        return Session::class;
    }

    static function create(string $key, $value){
        static::getContainer(static::getFacadedClass())->set($key, (string) $value);
    }
}