<?php

namespace Core\Database;


use Core\Config, PDO, PDOException;


abstract class Database{

    private static $_pdo = null;

    public static function connect(){
        if(static::$_pdo === null){
            try {
                static::$_pdo = new PDO(Config::getDSN(), Config::getUser(), Config::getPwd(), Config::getOptions());
            } catch (PDOException $e) {
                echo '[Database] Connexion échouée : ' . $e->getMessage();
            }
        }
    }

    public static function close(){
        static::$_pdo = null;
    }

    public static function getInstance(): PDO{
        if(static::$_pdo === null){
            static::connect();
        }
        return static::$_pdo;
    }

    public static function raw($sql = 'SELECT NOW();',$param = []){
        $query = static::$_pdo->prepare($sql);
        $query->execute($param);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

}