<?php

namespace App\Schema;

use Core\Facade\Contracts\TableFacade;
use Core\Mvc\Schema\Schema;

class IndexSchema extends Schema {

    public function __construct(){

        $table = TableFacade::create('film');

        $table->field('id')
            ->autoIncrement();

        $table->field('title')
            ->type('varchar')
            ->length(255);

        $table->field('date')
            ->type('date');

        $table->field('rate')
            ->type('int')
            ->length(2);

        $table->field('realisateur')
            ->type('int')
            ->manyToOne('realisateur', 'id')
                ->defaultFormSelection('name');

        $table->field('description')
            ->type('text')
            ->length('1000');

        $table->field('vote')
            ->type('int')
            ->length('2');

        $table->field('affiche')
            ->type('varchar');

        parent::__construct($table);
    }

}