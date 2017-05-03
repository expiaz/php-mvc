<?php
namespace Core;

use ArrayAccess;
use PDO;

final class Config implements ArrayAccess {

    private $config = [];

    public function __construct()
    {
        $this->config = [
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

        $db = $this->config['database'];
        $this->config['database']['dsn'] = "{$db['sgbd']}:host={$db['host']};dbname={$db['name']};charset={$db['charset']}";
    }


    public function set($key, $value){
        $this->config[(string) $key] = $value;
    }

    public function get($key){
        if($this->exists($key)){
            $value = $this->config[$key];
            if($value instanceof Closure)
                return call_user_func($value, $this);
            return $value;
        }
        throw new Exception("Out of bound");
    }

    public function delete($key){
        if($this->exists($key))
            unset($this->config[$key]);
    }

    public function exists($key){
        return isset($this->config[$key]);
    }

    public function __set($key, $value){
        $this->set($key, $value);
    }

    public function __get($key = null){
        return $this->get($key);
    }

    public function __call($method, $param = []){
        if(preg_match('/^(get|set)$/',substr($method, 0, 3))){
            $m = lcfirst(substr($method, 3));
            switch (substr($method, 0, 3)){
                case 'get':
                    return $this->get($m);
                case 'set':
                    if(count($param))
                        return $this->set($m, $param[0]);
                    return;
            }
        }
        if(count($param)){
            return $this->set($method, $param[0]);
        }
        return $this->get($method);
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            return;
        }
        $this->set($offset, $value);
    }

    public function offsetExists($offset) {
        return $this->exists($offset);
    }

    public function offsetUnset($offset) {
        $this->delete($offset);
    }

    public function offsetGet($offset) {
        return $this->get($offset);
    }

}