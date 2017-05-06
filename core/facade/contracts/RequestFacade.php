<?php

namespace Core\Facade\Contracts;

use Core\Facade\Facade;
use Core\Factory\RequestFactory;
use Core\Http\Request;
use Core\Utils\HttpParameterBag;

class RequestFacade extends Facade {


    static function getFacadeAccessor(): string
    {
        return 'Request';
    }

    static function getFacadedClass(): string
    {
        return Request::class;
    }

    static function create(HttpParameterBag $b){
        return static::getContainer(RequestFactory::class)->create($b);
    }

}