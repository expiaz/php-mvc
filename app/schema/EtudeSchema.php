<?php

namespace App\Schema;

use Core\Facade\Contracts\TableFacade;
use Core\Mvc\Schema\Schema;

class EtudeSchema extends Schema
{

    public function __construct()
    {

        $table = TableFacade::create('etude');

        $table->field('id_etudiant')
            ->primaryKey();

        $table->field('id_convention')
            ->primaryKey();

        $table->field('grade')
            ->type('int');

        $table->field('remuneration')
            ->type('int');

        $table->field('remunere')
            ->type('boolean');

        parent::__construct($table);

    }

}