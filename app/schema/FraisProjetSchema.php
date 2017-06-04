<?php

namespace App\Schema;

use Core\Facade\Contracts\TableFacade;
use Core\Mvc\Schema\Schema;

class FraisProjetSchema extends Schema
{

    public function __construct()
    {

        $table = TableFacade::create('frais_projet');

        $table->field('id_convention')
            ->primaryKey()
            ->manyToOne('convention', 'id');

        $table->field('id_etudiant')
            ->primaryKey()
            ->manyToOne('etudiant', 'id');

        $table->field('id_frais')
            ->primaryKey()
            ->oneToOne('frais', 'id');

        parent::__construct($table);

    }

}