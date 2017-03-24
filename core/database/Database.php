<?php

namespace Core\Database;


use Core\Config, PDO, PDOException;


abstract class Database{

    private static $_pdo = null;

    public static function connect(){
        if(self::$_pdo === null){
            try {
                self::$_pdo = new PDO(Config::$database['dsn'], Config::$database['user'], Config::$database['password'], Config::$database['options']);
            } catch (PDOException $e) {
                echo '[Database] Connexion échouée : ' . $e->getMessage();
            }
        }
    }

    public static function getInstance(){
        if(self::$_pdo === null){
            self::connect();
        }
        return self::$_pdo;
    }

    public static function raw($sql = 'SELECT NOW();',$param = []){
        $query = self::$_pdo->prepare($sql);
        $query->execute($param);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

}