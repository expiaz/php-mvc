<?php

namespace Core\Mvc\Schema;

use Core\Database\Database;
use Core\Database\Orm\ORM;
use Core\Database\Orm\Schema\Table;

abstract class Schema{

    protected $table;
    protected $schema;
    protected $statement;

    public function __construct(Table $t)
    {
        $this->table = $t;
        $this->schema = $t->schema();
        $this->statement = $t->statement();
        $this->orm = new ORM(container(Database::class), $this);
    }

    public function up(){
        $this->orm->create();
    }

    public function down(){
        $this->orm->drop();
    }

    public function table(): Table{
        return $this->table;
    }

    public function schema(): array{
        return $this->schema;
    }

    public function statement(): array{
        return $this->statement;
    }

}