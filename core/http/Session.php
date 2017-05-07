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

    public function beforeEach()
    {
        return static::$on === true;
    }

    public function reset(){
        session_destroy();
        static::$on = false;
    }

}