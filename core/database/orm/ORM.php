<?php

namespace Core\Database\Orm;

use Core\Database\Database;
use Core\Database\Orm\Schema\Schema;

final class ORM{

    private $schema;
    private $pdo;

    public function __construct(Database $pdo, Schema $schema)
    {
        $this->pdo = $pdo->getConnection();
        $this->schema = $schema;
    }

    public function create($class){
        $sql = $this->schema->get($class)->statement();
        return $this->pdo->query($sql) ? true : false;
    }

    public function drop($class){
        $sql = "DROP TABLE {$this->schema->get($class)->schema()['table']};";
        return $this->pdo->query($sql) ? true : false;
    }

}