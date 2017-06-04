<?php

namespace App\Schema;

use Core\Facade\Contracts\TableFacade;
use Core\Mvc\Schema\Schema;

class AccompteProjetSchema extends Schema
{

    public function __construct()
    {

        $table = TableFacade::create('accompte_projet');

        $table->field('id_accompte')
            ->primaryKey()
            ->oneToOne('accompte', 'id');

        $table->field('id_etudiant')
            ->primaryKey()
            ->manyToOne('etudiant', 'id');

        $table->field('id_convention')
            ->primaryKey()
            ->manyToOne('convention', 'id');

        parent::__construct($table);

    }

}