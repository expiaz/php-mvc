<?php

namespace App\Schema;

use Core\Facade\Contracts\TableFacade;
use Core\Mvc\Schema\Schema;

class FilmSchema extends Schema {

    public function __construct(){

        $table = TableFacade::create('film');

        $table->field('id')
            ->autoIncrement()
            ->manyToMany('acteur', 'id', 'INT')
                ->defaultFormSelection('name');

        $table->field('title')
            ->type('varchar')
            ->length(255);

        $table->field('date')
            ->type('date');

        $table->field('rate')
            ->type('int')
            ->length(10);

        $table->field('realisateur')
            ->type('int')
            ->manyToOne('realisateur', 'id')
                ->defaultFormSelection('name');

        $table->field('description')
            ->type('text')
            ->length('1000');

        $table->field('affiche')
            ->type('file');

        parent::__construct($table);
    }

}