<?php

namespace Core\App;

use ArrayAccess;
use Closure;
use Core\Database\Database;
use Core\Helper;
use Core\Mvc\Schema\Schema;
use Core\Mvc\Controller\Controller;
use Core\Mvc\Model\Model;
use Core\Mvc\Repository\Repository;

class Container implements ArrayAccess {

    private $container;

    public function __construct()
    {
        $this->container = [];
        $this->container['container'] = $this;
    }

    private function normalize(&$key){
        if(is_array($key))
            $key = (object) $key;
        if(is_object($key))
            $key = get_class($key);
    }

    public function getServices(){
        return array_keys($this->container);
    }

    public function singleton(Closure $singleton){
        return call_user_func($singleton, $this);
    }

    public function factory(Closure $factory){

    }

    public function set($key, $value){
        $this->normalize($key);
        $this->container[(string) $key] = $value;
    }

    public function get($key){
        $this->normalize($key);
        if($this->exists($key)){
            $value = $this->container[$key];
            if($value instanceof Closure)
                return call_user_func($value, $this);
            return $value;
        }
        return $this->resolve($key);
    }

    public function delete($key){
        $this->normalize($key);
        if($this->exists($key))
            unset($this->container[$key]);
    }

    public function exists($key){
        $this->normalize($key);
        return isset($this->container[$key]);
    }

    public function __set($key, $value){
        $this->set($key, $value);
    }

    public function __get($key = null){
        return $this->get($key);
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

    public function make($service){
        return $this->get($service);
    }

    public function resolve($key){
        $this->normalize($key);

        if(!$this->exists($key)){
            $matches = [];
            if(!preg_match('/App[\\\](Model|Controller|Schema|Repository)/', $key, $matches))
                throw new \Exception("can't resolve other class than base mvc app : {$key}");

            switch ($matches[1]){
                case 'Model':
                    $this->set($key, function (Container $c) use ($key):Model {
                        return new $key($c->resolve($c->get(Helper::class)->getSchemaNs($key)));
                    });
                    break;
                case 'Controller':
                    $this->set($key, $this->singleton(function (Container $c) use ($key):Controller {
                        return new $key($c, $c->resolve($c->get(Helper::class)->getRepositoryNs($key)));
                    }));
                    break;
                case 'Schema':
                    $this->set($key, $this->singleton(function (Container $c) use ($key):Schema {
                        return new $key();
                    }));
                    break;
                case 'Repository':
                    $this->set($key, $this->singleton(function (Container $c) use ($key):Repository {
                        return new $key($c->get(Database::class), $c->get(Helper::class)->getModelNs($key), $c->resolve($c->get(Helper::class)->getSchemaNs($key)) );
                    }));
                    break;
            }
        }

        return $this->get($key);
    }

}