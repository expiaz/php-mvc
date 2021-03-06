<?php

namespace Core\Database;

use Core\Config;
use Core\Exception\NoDataFoundException;
use Core\Utils\DataContainer;
use PDO, PDOException;
use PDOStatement;


final class Database{

    private $pdo;

    public function __construct(Config $config)
    {
        try {
            $this->pdo = new PDO($config->get('database')['dsn'], $config->get('database')['user'], $config->get('database')['password'], $config->get('database')['options']);
        } catch (PDOException $e) {
            echo '[Database] Connexion échouée : ' . $e->getMessage();
            exit(1);
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    public function close()
    {
        $this->pdo = null;
    }

    public function query(string $sql, int $mode = PDO::FETCH_OBJ): PDOStatement
    {
        return $this->pdo->query($sql, $mode);
    }

    public function execute(string $sql, array $parameters = []): bool
    {
        $query = $this->pdo->prepare($sql);
        return $query->execute($parameters);
    }

    public function fetch(string $sql, array &$parameters = []): UppletContainer
    {
        return $this->fetchAll($sql, $parameters)[0];
        /*$query = $this->pdo->prepare($sql);
        $query->execute($parameters);
        $query->setFetchMode(PDO::FETCH_CLASS, UppletContainer::class);
        if($result = $query->fetch()){
            return $result;
        }
        throw new \Exception("[Database::fetch] error while fetching");*/
    }

    public function &fetchAll(string $sql, array &$parameters = []): array
    {
        $query = $this->pdo->prepare($sql);
        $query->execute($parameters);
        $upplets = $query->fetchAll(PDO::FETCH_CLASS, UppletContainer::class);

        if(count($upplets) === 0){
            throw new NoDataFoundException("[Repository::fetchAll] No upplets found : sql request : {$sql}, bindings : " . print_r($parameters, true));
        }

        return $upplets;
    }

    public function raw(string $sql = 'SELECT NOW();', array &$param = []): array
    {
        $query = $this->pdo->prepare($sql);
        $query->execute($param);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function getLastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

}