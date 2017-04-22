<?php

namespace Core;

use Core\Cache;
use Core\Config;
use Core\Database\Database;
use Core\Database\Orm\ORM;
use Core\Database\Orm\Schema\Schema;
use Core\Form\FormBuilder;
use Core\Helper;
use Core\Http\Cookie;
use Core\Http\Query;
use Core\Http\Router;
use Core\Http\Session;
use Core\Http\Url;

final class App
{

    private static $instance;

    private $container;

    private function __construct($httpParameters)
    {
        require_once CORE . 'Bootstrapper.php';
        $this->container = new Container();
        $this->register();
        $this->launch($httpParameters);
    }

    public static function create($httpParameters): App{
        return new static($httpParameters);
    }

    public static function init($httpParameters): App{
        if(is_null(static::$instance))
            static::$instance = new static($httpParameters);
        return static::$instance;
    }

    public static function getInstance(): App{
        if(is_null(static::$instance))
            static::init('');
        return static::$instance;
    }

    public function register()
    {
        /*
         * Self
         */
        $this->container[App::class] = $this;
        $this->container['app'] = function (Container $c) {
            return $c[App::class];
        };

        /*
         * Helper
         */
        $this->container[Helper::class] = $this->container->singleton(function (Container $c) {
            return new Helper();
        });
        $this->container['helper'] = function (Container $c) {
            return $c[Helper::class];
        };

        /*
         * Config
         */
        $this->container[Config::class] = $this->container->singleton(function (Container $c){
            return new Config();
        });
        $this->container['config'] = function (Container $c) {
            return $c[Config::class];
        };

        /*
         * Cache
         */
        $this->container[Cache::class] = $this->container->singleton(function (Container $c) {
            return new Cache();
        });
        $this->container['cache'] = function (Container $c) {
            return $c[Cache::class];
        };

        /*
         * Cookie
         */
        $this->container[Cookie::class] = $this->container->singleton(function (Container $c) {
            return new Cookie();
        });
        $this->container['cookie'] = function (Container $c) {
            return $c[Cookie::class];
        };

        /*
         * Session
         */
        $this->container[Session::class] = $this->container->singleton(function (Container $c) {
            return new Session();
        });
        $this->container['session'] = function (Container $c) {
            return $c[Session::class];
        };

        /*
         * Database
         */
        $this->container[Database::class] = function (Container $c) {
            return new Database($c['config']['database']['dsn'], $c['config']['database']['user'], $c['config']['database']['password'], $c['config']['database']['options']);
        };
        $this->container['database'] = function (Container $c) {
            return $c[Database::class];
        };

        /*
         * Database Singleton
         */
        $this->container['database.singleton'] = $this->container->singleton(function (Container $c) {
            return $c['database'];
        });

        /*
         * Query
         */
        $this->container[Query::class] = function (Container $c) {
            return new Query(new Url());
        };
        $this->container['query'] = function (Container $c) {
            return $c[Query::class];
        };

        /*
         * Router
         */
        $this->container[Router::class] = $this->container->singleton(function (Container $c) {
            return new Router();
        });
        $this->container['router'] = function (Container $c) {
            return $c[Router::class];
        };

        /*
         * TODO SCHEMA
         */


        /*
         * ORM
         */
        $this->container[ORM::class] = $this->container->singleton(function (Container $c) {
            return new ORM($c[Database::class], $c[Schema::class]);
        });
        $this->container['orm'] = function (Container $c) {
            return $c[ORM::class];
        };

        /*
         * FormBuilder
         */
        $this->container[FormBuilder::class] = $this->container->singleton(function (Container $c) {
            return new FormBuilder();
        });
        $this->container['form'] = function (Container $c) {
            return $c[FormBuilder::class];
        };

    }

    private function launch($httpParameters){
        new Bootstrapper();
        new Dispatcher($httpParameters);
    }

    public function make($service)
    {
        return $this->container[$service];
    }

    public function getContainer(): Container{
        return $this->container;
    }

}