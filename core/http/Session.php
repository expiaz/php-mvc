<?php

namespace Core\Http;

use ArrayAccess;
use Core\Utils\Interfaces\MagicAccess as MagicAccessInterface;
use Core\Utils\Traits\MagicAccess as MagicAccessTrait;

final class Session implements ArrayAccess, MagicAccessInterface {

    use MagicAccessTrait;

    private static $on = false;


    public function activate(){
        if(static::$on === true)
            return;

        session_start();
        static::$on = true;
    }

    public function __construct()
    {
        $this->activate();
        $this->container = $this->initializeContainer();
    }

    private function &initializeContainer()
    {
        return $_SESSION;
    }

    public function set(string $key, $value)
    {
        if(!$this->beforeEach($key))
            return;

        $this->normalize($key);

        $_SESSION[$key] = $value;
    }

    public function get(string $key, $default = null)
    {
        if(!$this->beforeEach($key))
            return;

        $this->normalize($key);
        if($this->exists($key)){
            return $_SESSION[$key];
        }

        return $default;
    }

    public function exists(string $key)
    {
        if(!$this->beforeEach($key))
            return;

        $this->normalize($key);
        return isset($_SESSION[$key]);
    }

    public function beforeEach(string &$key)
    {
        return static::$on === true;
    }

    public function reset(){
        session_destroy();
        static::$on = false;
    }

}