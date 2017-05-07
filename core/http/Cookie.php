<?php

namespace Core\Http;

use ArrayAccess;
use Core\Utils\Interfaces\MagicAccess as MagicAccessInterface;
use Core\Utils\Traits\MagicAccess as MagicAccessTrait;

final class Cookie implements ArrayAccess, MagicAccessInterface {

    use MagicAccessTrait;

    public function &initializeContainer(){
        return $_COOKIE;
    }

    public function set(string $k, $v, $expire = null){
        if(is_null($expire))
            $expire = time() + 3600;
        setcookie((string) $k,(string) $v,$expire);
    }

    public function delete(string $k){
        $this->unset($k);
    }

    public  function flush(){
        $this->reset();
    }

}