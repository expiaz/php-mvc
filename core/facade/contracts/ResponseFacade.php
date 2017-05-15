<?php

namespace Core\Facade\Contracts;

use Core\Facade\Facade;
use Core\Http\Response;

class ResponseFacade extends Facade {


    static function getFacadeAccessor(): string
    {
        return 'Response';
    }

    static function getFacadedClass(): string
    {
        return Response::class;
    }

}