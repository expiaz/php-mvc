<?php

namespace App\Schema;

use Core\Database\Orm\Schema\Table;
use Core\Mvc\Schema\Schema;

class IndexSchema extends Schema {

    public function __construct(){

        $table = new Table('index');

        $table->field('id')
            ->autoIncrement();

        $table->field('name')
            ->type('varchar')
             ->nullable();

        $table->field('age')
            ->type('int');

        parent::__construct($table);
    }

}