<?php

namespace Core\Database;


use PDO, PDOException;


final class Database{

    private $pdo;

    public function __construct($dsn, $user, $pwd, $opts)
    {
        try {
            $this->pdo = new PDO($dsn, $user, $pwd, $opts);
        } catch (PDOException $e) {
            echo '[Database] Connexion échouée : ' . $e->getMessage();
            exit(1);
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    public function getConnection(): PDO{
        return $this->pdo;
    }

    public function close(){
        $this->pdo = null;
    }

    public function raw($sql = 'SELECT NOW();', $param = []){
        $query = $this->pdo->prepare($sql);
        $query->execute($param);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

}