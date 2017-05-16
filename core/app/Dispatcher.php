<?php
namespace Core\App;

use Core\App;
use Core\Factory\RequestFactory;
use Core\Http\Response;
use Core\Utils\HttpParameterBag;
use Core\Factory\LoaderFactory;
use Core\Http\Query;
use Core\Http\Request;
use Core\Http\Route\Route;
use Core\Http\Router;

final class Dispatcher{

    private $url;
    private $route;
    private $request;
    private $response;
    private $container;

    public function __construct(Container $container, string $url){
        $this->url = $url;
        $this->container = $container;
    }

    public function dispatch(){

        $this->route = $this->container[Router::class]->apply($this->url, $_SERVER['REQUEST_METHOD']);

        $parameters = $this->route->applyArguments($this->url);

        $loader = $this->container[LoaderFactory::class]->create($this->route, $this->url);

        $this->container->set(Response::class, $this->container->singleton(function (Container $c){
            return new Response();
        }));

        $this->request = $request = $this->container[RequestFactory::class]->create(new HttpParameterBag($parameters), $this->route);
        $this->response = $this->container[Response::class];

        $this->container->set(Request::class, $this->container->singleton(function (Container $c) use ($request){
            return $request;
        }));

        return $loader->load($this->request, $this->response);

    }

}