<?php

namespace App\Schema;

use Core\Facade\Contracts\TableFacade;
use Core\Mvc\Schema\Schema;

class IndexSchema extends Schema {

    public function __construct(){

        $table = TableFacade::create('realisateur');

        $table->field('id')
            ->autoIncrement()
            ->manyToOne('film', 'id');

        $table->field('name')
            ->type('varchar')
            ->length(255)
            ->defaultFormSelection();

        parent::__construct($table);
    }

}