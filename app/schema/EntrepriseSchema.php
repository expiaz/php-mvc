<?php

namespace App\Schema;

use Core\Facade\Contracts\TableFacade;
use Core\Mvc\Schema\Schema;

class EntrepriseSchema extends Schema
{

    public function __construct()
    {

        $table = TableFacade::create('entreprise');

        $table->field('id')
            ->autoIncrement();

        $table->field('adresse')
            ->type('varchar')
            ->length(255);

        $table->field('siret')
            ->type('varchar')
            ->length(50);

        $table->field('tel')
            ->type('varchar')
            ->length(10);

        $table->field('raison')
            ->type('varchar')
            ->length(10);

        $table->field('user')
            ->type('int')
            ->oneToOne('user', 'id');

        parent::__construct($table);

    }

}