<?php

namespace App\Entity\Schema;

use Core\Database\Orm\Schema\Table;

class FilmSchema{

    public static function describe(){

        $table = new Table('film');

        $table->addField('id')
            ->primaryKey()
            ->type('int')
            ->autoIncrement();

        $table->addField('acteur')
            ->manyToMany('acteur','id');

        return $table->describe();
    }

}