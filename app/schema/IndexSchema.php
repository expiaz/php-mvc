<?php

namespace App\Schema;

use Core\Facade\Contracts\TableFacade;
use Core\Mvc\Schema\Schema;

class IndexSchema extends Schema {

    public function __construct(){

        $table = TableFacade::create('index');

        $table->field('id')
            ->autoIncrement();

        parent::__construct($table);
    }

}