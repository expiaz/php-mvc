<?php

namespace Core;

use Core\Database\Database;
use Core\Form\FormBuilder;
use Core\Http\Cookie;
use Core\Http\Query;
use Core\Http\Router;
use Core\Http\Session;
use Core\Http\Url;

final class App
{

    private static $instance = null;

    private $container;

    private function __construct($httpParameters)
    {
        static::$instance = $this;
        $this->container = new Container();
        $this->register();
        $this->launch($httpParameters);
    }

    public static function init($httpParameters): App{
        if(is_null(static::$instance))
            return new static($httpParameters);
    }

    public static function getInstance(): App{
        if(is_null(static::$instance))
            throw new \Exception("App may have not been initialized, use App::init(url)");
        return static::$instance;
    }

    public function register()
    {
        /*
         * Self
         */
        $this->container[App::class] = $this;
        $this->container['app'] = function (Container $c):App {
            return $c[App::class];
        };

        /*
         * Helper
         */
        $this->container[Helper::class] = $this->container->singleton(function (Container $c):Helper {
            return new Helper();
        });
        $this->container['helper'] = function (Container $c):Helper {
            return $c[Helper::class];
        };

        /*
         * Config
         */
        $this->container[Config::class] = $this->container->singleton(function (Container $c):Config {
            return new Config();
        });
        $this->container['config'] = function (Container $c):Config {
            return $c[Config::class];
        };

        require_once CORE . 'shared' . DS . 'webConstants.php';

        /*
         * Cache
         */
        /*
        $this->container[Cache::class] = $this->container->singleton(function (Container $c):Cache {
            return new Cache();
        });
        $this->container['cache'] = function (Container $c):Cache {
            return $c[Cache::class];
        };
        */

        /*
         * Cookie
         */
        $this->container[Cookie::class] = $this->container->singleton(function (Container $c):Cookie {
            return new Cookie();
        });
        $this->container['cookie'] = function (Container $c):Cookie {
            return $c[Cookie::class];
        };

        /*
         * Session
         */
        $this->container[Session::class] = $this->container->singleton(function (Container $c):Session {
            $s = new Session();
            $s->activate();
            return $s;
        });
        $this->container['session'] = function (Container $c):Session {
            return $c[Session::class];
        };

        /*
         * Database
         */
        $this->container[Database::class] = function (Container $c):Database {
            return new Database($c['config']['database']['dsn'], $c['config']['database']['user'], $c['config']['database']['password'], $c['config']['database']['options']);
        };
        $this->container['database'] = function (Container $c):Database {
            return $c[Database::class];
        };

        /*
         * Database Singleton
         */
        $this->container['database.singleton'] = $this->container->singleton(function (Container $c):Database {
            return $c['database'];
        });

        /*
         * Query
         */
        $this->container[Query::class] = function (Container $c):Query {
            return new Query(new Url());
        };
        $this->container['query'] = function (Container $c):Query {
            return $c[Query::class];
        };

        /*
         * Router
         */
        $this->container[Router::class] = $this->container->singleton(function (Container $c):Router {
            return new Router();
        });
        $this->container['router'] = function (Container $c):Router {
            return $c[Router::class];
        };

        /*
         * TODO SCHEMA
         */


        /*
         * ORM
         */

        /*
        $this->container[ORM::class] = $this->container->singleton(function (Container $c):ORM {
            return new ORM($c[Database::class], $c[Schema::class]);
        });
        $this->container['orm'] = function (Container $c):ORM {
            return $c[ORM::class];
        };
        */

        /*
         * FormBuilder
         */
        $this->container[FormBuilder::class] = $this->container->singleton(function (Container $c):FormBuilder {
            return new FormBuilder();
        });
        $this->container['form'] = function (Container $c):FormBuilder {
            return $c[FormBuilder::class];
        };

    }

    private function launch($httpParameters){
        require_once APP . 'route.php';
        new Dispatcher($this->container, $httpParameters);
    }

    public function make($service)
    {
        return $this->container[$service];
    }

    public function getContainer(): Container{
        return $this->container;
    }

}