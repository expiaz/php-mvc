<?php
namespace Core;

use PDO;

abstract class Config{

    static public $database = [
        'sgbd' => 'mysql',
        'bd' => 'webphp',
        'host' => 'localhost',
        'charset' => 'UTF8',
        'user' => 'root',
        'password' => '',
        'options' => [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ]
        ];

    public static function getDSN(){
        return self::$database['sgbd'] . ":host=" . self::$database['host'] . ";dbname=" . self::$database['bd'] . ";charset=" . self::$database['charset'];
    }

    public static function getUser(){
        return self::$database['user'];
    }

    public static function getPwd(){
        return self::$database['password'];
    }

    public static function getOptions(){
        return self::$database['options'];
    }

    public static function getDbName(){
        return self::$database['bd'];
    }

}