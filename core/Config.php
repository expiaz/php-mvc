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
        return static::$config['sgbd'] . ":host=" . static::$config['host'] . ";dbname=" . static::$config['bd'] . ";charset=" . static::$config['charset'];
    }

    public static function getUser(){
        return static::$config['user'];
    }

    public static function getPwd(){
        return static::$config['password'];
    }

    public static function getOptions(){
        return static::$config['options'];
    }

    public static function getDbName(){
        return static::$config['bd'];
    }

    public static function getBaseURI(){
        return static::$config['baseURI'];
    }

}