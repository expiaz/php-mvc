<?php

namespace Core\Database\Orm;

use Core\Database\Database;
use Core\Mvc\Schema\Schema;

final class ORM{

    private $schema;
    private $pdo;

    public function __construct(Database $pdo, Schema $schema)
    {
        $this->pdo = $pdo->getConnection();
        $this->schema = $schema;
    }

    public function create(){
        $sql = $this->schema->statement();
        return $this->pdo->query($sql) ? true : false;
    }

    public function drop(){
        $sql = "DROP TABLE {$this->schema->schema()['table']};";
        return $this->pdo->query($sql) ? true : false;
    }

}