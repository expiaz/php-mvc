<?php

namespace Core\App;


use Core\App;
use Core\Factory\RequestFactory;
use Core\Helper;
use Core\Http\Request;
use Core\Http\Response;
use Core\Utils\HttpParameterBag;
use Core\Http\Route\Route;

/**
 * Class Loader
 * @package Core\App
 * Load the service asked (controller/method)
 */
class Loader{

    private $container;

    private $route;

    private $url;

    public function __construct(Container $container, Route $route, string $url = null)
    {
        $this->container = $container;
        $this->route = $route;
        $this->url = $url;
    }

    public function load(Request $request = null, Response $response = null){

        if(is_null($request))
            $request = $this->container[Request::class];
        if(is_null($response))
            $response = $this->container[Response::class];

        return $this->route->applyMiddlewares($request, $response);

    }



}