<?php

namespace Core\Facade\Contracts;

use Core\Http\Cookie;
use Core\Facade\Facade;

class CookieFacade extends Facade {

    static function getFacadeAccessor(): string
    {
        return 'Cookie';
    }

    static function getFacadedClass(): string
    {
        return Cookie::class;
    }

    static function create(string $key, $value){
        static::getContainer(static::getFacadedClass())->set($key, (string) $value);
    }
}