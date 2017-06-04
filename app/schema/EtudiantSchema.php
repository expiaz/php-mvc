<?php

namespace App\Schema;

use Core\Facade\Contracts\TableFacade;
use Core\Mvc\Schema\Schema;

class EtudiantSchema extends Schema
{

    public function __construct()
    {

        $table = TableFacade::create('etudiant');

        $table->field('id')
            ->autoIncrement();

        $table->field('prenom')
            ->type('varchar')
            ->length(50);

        $table->field('num_etu')
            ->type('int')
            ->length(10);

        $table->field('num_ss')
            ->type('int')
            ->length(15);

        $table->field('birthday')
            ->type('date');

        $table->field('user')
            ->type('int')
            ->oneToOne('user', 'id');

        parent::__construct($table);

    }

}