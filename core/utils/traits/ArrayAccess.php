<?php

namespace Core\Utils\Traits;

trait ArrayAccess{

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
        $this->unset($offset);
    }

    public function offsetGet($offset) {
        return $this->get($offset);
    }

}