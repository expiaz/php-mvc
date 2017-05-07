<?php
namespace Core;

use ArrayAccess;;
use PDO;
use Core\Utils\Interfaces\MagicAccess as MagicAccessInterface;
use Core\Utils\Traits\MagicAccess as MagicAccessTrait;

final class Config implements ArrayAccess, MagicAccessInterface {

    use MagicAccessTrait;

    public function &initializeContainer()
    {

        $c = [
            'database' => [
                'sgbd' => 'mysql',
                'name' => 'webphp',
                'host' => 'localhost',
                'charset' => 'UTF8',
                'user' => 'root',
                'password' => '',
                'options' => [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ]
            ],
            'url' => [
                'base' => '/'
            ]
        ];

        $db = $c['database'];
        $c['database']['dsn'] = "{$db['sgbd']}:host={$db['host']};dbname={$db['name']};charset={$db['charset']}";

        return $c;
    }

}