<?php

namespace App\Schema;

use Core\Facade\Contracts\TableFacade;
use Core\Mvc\Schema\Schema;

class FraisSchema extends Schema
{

    public function __construct()
    {

        $table = TableFacade::create('frais');

        $table->field('id')
            ->primaryKey();

        $table->field('date')
            ->type('date');

        $table->field('prix')
            ->type('int');

        $table->field('rembourse')
            ->type('boolean');

        parent::__construct($table);

    }

}