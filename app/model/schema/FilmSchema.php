<?php

namespace App\Model\Schema;

use Core\Database\Orm\Schema\Schema;
use Core\Database\Orm\Schema\Table;

class FilmSchema{

    /*
    public function __construct(){

        Schema::create(FilmSchema::class, 'tableName', function (Table $table){
            $table->prefix('f');

            $table->addField('id')
                ->type('int')
                ->primaryKey();

            $table->addField('name')
                ->type('varchar')
                ->length(200);
        });

    }
    */

    public function schema(): Table{

        $table = new Table('film');

        $table->field('id')
            ->type('int')
            ->primaryKey();

        $table->field('affiche')
            ->type('film')
            ->nullable();

        $table->field('mailing')
            ->type('boolean');

        return $table;
    }

}