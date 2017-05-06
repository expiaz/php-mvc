<?php

namespace Core\Facade\Contracts;

use Core\Facade\Facade;
use Core\Helper;

class HelperFacade extends Facade {


    static function getFacadeAccessor(): string
    {
        return 'Helper';
    }

    static function getFacadedClass(): string
    {
        return Helper::class;
    }
}