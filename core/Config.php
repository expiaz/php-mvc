<?php
namespace Core;

use PDO;

abstract class Config{

    static public $database = [
        'dsn' => 'mysql:host=localhost;dbname=webphp;charset=UTF8',
        'bd' => 'webphp',
        'user' => 'root',
        'password' => '',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    ];


    public static function getDbName(){
        return self::$database['bd'];
    }

}