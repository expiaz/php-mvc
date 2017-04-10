<?php

namespace App\Entity\Schema;

use Core\Database\Orm\Schema\Table;

class FilmSchema{

    public static function describe(){

        $table = new Table('film');

        $table->prefix('f');

        $table->addField('id')
            ->type('int');

        $table->addConstraint('name')
            ->on('field','field2')
            ->primary();

        $table->addConstraint('name')
            ->on('field')
            ->autoIncrement();

        $table->addConstraint('name')
            ->on('field')
            ->manyToMany('table','field')
            ->addField();

        return $table->describe();
    }

}