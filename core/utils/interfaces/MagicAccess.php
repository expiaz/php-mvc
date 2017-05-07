<?php

namespace Core\Utils\Interfaces;

interface MagicAccess{

    public function set(string $key, $value);

    public function get(string $key);

    public function exists(string $key);

    public function unset(string $key);

    public function reset();

    public function __set(string $key, $value);

    public function __get(string $key);

    public function __call(string $method, $param = []);

}