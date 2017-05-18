<?php

namespace App\Schema;

use Core\Facade\Contracts\TableFacade;
use Core\Mvc\Schema\Schema;

class RealisateurSchema extends Schema {

    public function __construct(){

        $table = TableFacade::create('realisateur');

        $table->field('id')
            ->autoIncrement();

        $table->field('name')
            ->type('varchar')
            ->length(255);

        parent::__construct($table);
    }

}