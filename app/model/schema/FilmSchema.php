<?php

namespace App\Model\Schema;

use Core\Mvc\Schema\Schema;
use Core\Database\Orm\Schema\Table;

class FilmSchema extends Schema{

    public function __construct()
    {
        $table = new Table('film');

        $table->field('id')
            ->type('int')
            ->primaryKey();

        $table->field('affiche')
            ->type('film')
            ->nullable();

        $table->field('mailing')
            ->type('boolean');

        parent::__construct($table);
    }

}