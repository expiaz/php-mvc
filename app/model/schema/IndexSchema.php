<?php

namespace App\Model\Schema;

use Core\Database\Orm\Schema\Table;

class IndexSchema{


    public function schema(): Table{

        $table = new Table('index');

        $table->addField('id')
            ->type('int')
            ->autoIncrement();

        $table->addField('name')
            ->type('varchar')
            ->length(200);

        return $table;
    }

}