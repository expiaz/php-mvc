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
use Core\Utils\Traits\MagicAccess as MagicAccessTrait;
use Core\Utils\Interfaces\MagicAccess as MagicAccessInterface;

class Container implements ArrayAccess, MagicAccessInterface {

    use MagicAccessTrait;

    public function &initializeContainer()
    {
        $c = [];
        $c['container'] = $this;

        return $c;
    }

    public function getServices(){
        return array_keys($this->container);
    }

    public function singleton(Closure $singleton){
        return call_user_func($singleton, $this);
    }

    public function get(string $key){
        $this->normalize($key);
        if(! $this->exists($key))
            return;

        if($this->container[$key] instanceof Closure){
            return call_user_func($this->container[$key], $this);
        }

        return $this->container[$key];
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