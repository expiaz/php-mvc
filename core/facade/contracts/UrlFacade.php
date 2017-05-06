<?php

namespace Core\Facade\Contracts;

use Core\Facade\Facade;
use Core\Http\Url;

class UrlFacade extends Facade {


    static function getFacadeAccessor(): string
    {
        return 'Url';
    }

    static function getFacadedClass(): string
    {
        return Url::class;
    }

}