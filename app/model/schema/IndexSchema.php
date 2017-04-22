<?php

namespace App\Model\Schema;

use Core\Database\Orm\Schema\Table;

class IndexSchema{


    public function schema(): Table{

        $table = new Table('index');

        $table->field('id')
            ->autoIncrement();

        $table->field('affiche')
            ->type('file')
             ->nullable();

        $table->field('mailing')
            ->type('boolean');

        return $table;
    }

}