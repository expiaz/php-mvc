<?php

namespace App\Schema;

use Core\Facade\Contracts\TableFacade;
use Core\Mvc\Schema\Schema;

class IndexSchema extends Schema {

    public function __construct(){

        $table = TableFacade::create('index');

        $table->field('id')
            ->autoIncrement();

        $table->field('affiche')
            ->type('varchar')
            ->default('mon n\'affiche');

        $table->field('mailing')
            ->type('varchar');

        parent::__construct($table);
    }

}