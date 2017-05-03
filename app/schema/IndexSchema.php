<?php

namespace App\Schema;

use Core\Database\Orm\Schema\Table;
use Core\Mvc\Schema\Schema;

class IndexSchema extends Schema {

    public function __construct(){

        $table = new Table('index');

        $table->field('id')
            ->autoIncrement();

        $table->field('affiche')
            ->type('file')
             ->nullable();

        $table->field('mailing')
            ->type('boolean');

        parent::__construct($table);
    }

}