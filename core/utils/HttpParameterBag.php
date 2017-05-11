<?php

namespace Core\Utils;

use Core\Utils\Traits\MagicAccess as MagicAccessTrait;
use Core\Utils\Interfaces\MagicAccess as MagicAccessInterface;

class HttpParameterBag implements \ArrayAccess, MagicAccessInterface {

    use MagicAccessTrait;

    public function __construct(array $baseParameters = [])
    {
        $this->container = $this->initializeContainer();
        foreach ($baseParameters as $k => $v)
            $this->set($k, $v);
    }

    public function getBag(){
        return $this->getAll();
    }

    public function add($key, $parameter){
        $this->set($key, $parameter);
    }

    public function beforeEach(string &$key)
    {
        $key = $this->escape($key);
        return true;
    }

    private function escape($p){
        return is_array($p) ? array_map(array($this, 'escape'), $p) : htmlspecialchars($p);
    }

}