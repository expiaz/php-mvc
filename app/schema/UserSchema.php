<?php

namespace App\Schema;

use Core\Facade\Contracts\TableFacade;
use Core\Mvc\Schema\Schema;

class UserSchema extends Schema {

    public function __construct(){

        $table = TableFacade::create('user');

        $table->field('id')
            ->autoIncrement();

        $table->field('name')
            ->type('varchar')
            ->length(50);

        $table->field('login')
            ->type('varchar')
            ->length(50);

        $table->field('password')
            ->type('password');

        parent::__construct($table);
    }

}