<?php

namespace Core;

use ArrayAccess;
use Core\App\ObBuffer;
use Core\Facade\AliasesLoader;
use Core\Factory\RequestFactory;
use Core\App\Container;
use Core\App\Dispatcher;
use Core\Factory\LoaderFactory;
use Core\Database\Database;
use Core\Form\FormBuilder;
use Core\Http\Cookie;
use Core\Http\Query;
use Core\Http\Response;
use Core\Http\Router;
use Core\Http\Session;
use Core\Http\Url;
use Core\Mvc\View\View;

final class App implements ArrayAccess
{

    private static $instance = null;

    private $container;

    private $bufferOutput;

    private function __construct($httpParameters)
    {
        static::$instance = $this;
        //$this->bufferOutput = new ObBuffer();
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
            return new Session();
        });
        $this->container['session'] = function (Container $c):Session {
            return $c[Session::class];
        };

        /*
         * Database

        $this->container[Database::class] = function (Container $c):Database {
            return new Database($c->get(Config::class)->get('database')['dsn'], $c->get(Config::class)->get('database')['user'], $c->get(Config::class)->get('database')['password'], $c->get(Config::class)->get('database')['options']);
        };
        $this->container['database'] = function (Container $c):Database {
            return $c->get(Database::class);
        };

        $this->container['database.singleton'] = $this->container->singleton(function (Container $c):Database {
            return $c->get(Database::class);
        });
        */

        /*
         * Router
         */
        $this->container[Router::class] = $this->container->singleton(function (Container $c):Router {
            return new Router();
        });
        $this->container['router'] = function (Container $c):Router {
            return $c->get(Router::class);
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
        $this->container['formbuilder'] = function (Container $c):FormBuilder {
            return $c->get(FormBuilder::class);
        };

        $this->container[View::class] = function (Container $c): View{
            return new View($c);
        };
        $this->container['view'] = function (Container $c): View{
            return $c->get(View::class);
        };

        $this->container[Response::class] = $this->container->singleton(function (Container $c): Response{
            return new Response($_SERVER['SERVER_PROTOCOL']);
        });
        $this->container['response'] = function (Container $c): Response{
            return $c->get(Response::class);
        };


        /*
         * Factories

        $this->container[LoaderFactory::class] = $this->container->singleton(function (Container $c):LoaderFactory{
            return new LoaderFactory($c);
        });

        $this->container[RequestFactory::class] = $this->container->singleton(function (Container $c):RequestFactory{
            return new RequestFactory($c);
        });

        */

        new AliasesLoader();
    }

    public static function make(string $service){
        return static::getInstance()[$service];
    }

    public function launch($httpParameters){
        require_once APP . 'route.php';
        new Dispatcher($this->container, $httpParameters);
    }

    public function finish($returnStatement = null){


        $response = $this[Response::class];

        if(! is_null($returnStatement) && (is_string($returnStatement) || method_exists($returnStatement, '__toString')) ){
            $response->withBody((string) $returnStatement);
        }

        /*$debug = $this->bufferOutput->unbufferize();
        if(DEV){
            $response->write($debug);
        }*/

        if(! headers_sent()){
            http_response_code($response->getStatusCode());

            foreach ($response->getHeaders() as $header => $phrase){
                header(sprintf('%s: %s', $header, $phrase), false);
            }

            $contentLength = $response->getHeader(Response::CONTENT_LENGTH);
            $body = $response->getBody();
            $content = substr($body, 0, $contentLength);
            if($contentLength > 0 && strlen($content)){
                echo $content;
            }
        }
        else{
            throw new \Exception("App::finish Headers already sent : \n<br/>" . print_r(headers_list()));
        }
        exit(0);
    }

    public function getContainer(): Container{
        return $this->container;
    }

    public function offsetExists($offset)
    {
        return $this->container->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->container->offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->container->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->container->offsetUnset($offset);
    }

    public function __invoke()
    {
        return $this->offsetGet(count(func_get_args()) ? func_get_args()[0] : App::class);
    }

}