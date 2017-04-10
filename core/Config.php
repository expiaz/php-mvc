<?php
namespace Core;

use PDO;

abstract class Config{

    public static $config = [
        'sgbd' => 'mysql',
        'bd' => 'webphp',
        'host' => 'localhost',
        'charset' => 'UTF8',
        'user' => 'root',
        'password' => '',
        'options' => [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ],
        'baseURI' => '/'
        ];

    public static function getDSN(){
        return self::$config['sgbd'] . ":host=" . self::$config['host'] . ";dbname=" . self::$config['bd'] . ";charset=" . self::$config['charset'];
    }

    public static function getUser(){
        return self::$config['user'];
    }

    public static function getPwd(){
        return self::$config['password'];
    }

    public static function getOptions(){
        return self::$config['options'];
    }

    public static function getDbName(){
        return self::$config['bd'];
    }

    public static function getBaseURI(){
        return self::$config['baseURI'];
    }

}