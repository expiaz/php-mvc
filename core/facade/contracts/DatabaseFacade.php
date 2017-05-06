<?php

namespace Core\Facade\Contracts;

use Core\Database\Database;
use Core\Facade\Facade;

class DatabaseFacade extends Facade {


    static function getFacadeAccessor(): string
    {
        return 'Database';
    }

    static function getFacadedClass(): string
    {
        return Database::class;
    }
}