<?php

namespace Core\Database\Orm;

use Core\Database\Database;
use Core\Exception\SqlAlterException;
use Core\Mvc\Schema\Schema;
use Core\Utils\DataContainer;

final class ORM{

    private $schema;
    private $pdo;

    public static function transactionUp(Database $db , array $schemas){

        $pdo = $db->getConnection();

        $pdo->beginTransaction();

        foreach ($schemas as $schema){
            if($pdo->exec($schema->table()->tableStatement()) === false){
                $pdo->rollBack();
                throw new SqlAlterException("[ORM::create] problem with sql request : {$schema->tableStatement()}");
            }
        }

        foreach ($schemas as $schema){
            $statements = $schema->table()->constraintStatements();
            foreach ($statements as $statement){
                foreach ($statement as $request){
                    if($pdo->exec($request) === false){
                        $pdo->rollBack();
                        throw new SqlAlterException("[ORM::create] problem with sql request : {$request}");
                    }
                }
            }
        }

        $pdo->commit();

        return true;
    }

    public static function transactionDown(array $schemas){

    }

    public function __construct(Database $pdo, Schema $schema)
    {
        $this->pdo = $pdo->getConnection();
        $this->schema = $schema;
    }

    public function create(){
        $statements = $this->schema->statement();
        $this->pdo->beginTransaction();
        foreach ($statements as $statement){
            if($this->pdo->exec($statement) === false){
                $this->pdo->rollBack();
                throw new SqlAlterException("[ORM::create] problem with sql request : {$statement}");
            }
        }
        $this->pdo->commit();
        return true;
    }

    public function drop(){
        $sql = "DROP TABLE {$this->schema->schema()['table']};";
        if($this->pdo->exec($sql) === false){
            throw new SqlAlterException("[ORM::create] problem with sql request : {$sql}");
        }
        return true;
    }

}