<?php
namespace Core;

use PDO;

abstract class Config{

    static public $database = [
        'dsn' => 'mysql:host=localhost;dbname=webphp;charset=UTF8',
        'user' => 'root',
        'password' => '',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    ];

}