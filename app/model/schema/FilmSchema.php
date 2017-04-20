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

        $table->addField('id')
            ->type('int')
            ->primaryKey();

        $table->addField('name')
            ->type('varchar')
            ->length(200);

        return $table;
    }

}