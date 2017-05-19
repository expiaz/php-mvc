<?php

namespace App\Schema;

use Core\Facade\Contracts\TableFacade;
use Core\Mvc\Schema\Schema;

class ActeurSchema extends Schema {

    public function __construct(){

        $table = TableFacade::create('acteur');

        $table->field('id')
            ->autoIncrement();

        $table->field('name')
            ->type('varchar')
            ->length(75);

        parent::__construct($table);
    }

}