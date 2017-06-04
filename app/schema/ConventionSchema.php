<?php

namespace App\Schema;

use Core\Facade\Contracts\TableFacade;
use Core\Mvc\Schema\Schema;

class ConventionSchema extends Schema
{

    public function __construct()
    {

        $table = TableFacade::create('convention');

        $table->field('id')
            ->autoIncrement();

        $table->field('titre')
            ->type('varchar')
            ->length(100);

        $table->field('date_creation')
            ->type('date');

        $table->field('duree')
            ->type('int');

        $table->field('date_fin')
            ->type('date');

        $table->field('date_debut')
            ->type('date')
            ->nullable();

        $table->field('prix_j')
            ->type('double');

        $table->field('entreprise')
            ->type('int')
            ->manyToOne('entreprise', 'id');

        parent::__construct($table);

    }

}