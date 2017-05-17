<?php

namespace Core\Facade\Contracts;

use Core\Database\Orm\Schema\Table;
use Core\Facade\Facade;

class TableFacade extends Facade {


    static function getFacadeAccessor(): string
    {
        return 'Table';
    }

    static function getFacadedClass(): string
    {
        return Table::class;
    }

    static function create(string $name): Table{
        $ns = static::getFacadedClass();
        return new $ns($name);
    }

}