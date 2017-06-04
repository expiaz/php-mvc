<?php

namespace App\Schema;

use Core\Facade\Contracts\TableFacade;
use Core\Mvc\Schema\Schema;

class UserSchema extends Schema {

    public function __construct(){

        $table = TableFacade::create('user');

        $table->field('id')
            ->autoIncrement();

        $table->field('nom')
            ->type('varchar')
            ->length(50);

        $table->field('login')
            ->type('varchar')
            ->length(50);

        $table->field('password')
            ->type('varchar')
            ->length(255);

        $table->field('admin')
            ->type('boolean');

        parent::__construct($table);
    }

}