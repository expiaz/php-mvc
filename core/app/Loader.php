<?php

namespace Core\App;


use Core\App;
use Core\Factory\RequestFactory;
use Core\Helper;
use Core\Http\Request;
use Core\Utils\HttpParameterBag;
use Core\Http\Route\Route;

/**
 * Class Loader
 * @package Core\App
 * Load the service asked (controller/method)
 */
class Loader{

    private $controller;
    private $action;
    private $parameters;
    private $request;

    private $response;

    private $container;

    public function __construct(Container $container, Route $route, HttpParameterBag $parameters)
    {
        $this->container = $container;

        $this->parameters = $parameters;

        $request = $container[RequestFactory::class]->create($parameters, $route->getMethod());
        $this->request = $request;

        $this->container->set(Request::class, $this->container->singleton(function (Container $c) use ($request){
            return $request;
        }));

        switch ($route->getType()){
            case Route::CLOSURE:
                $this->response = call_user_func_array($route->getHandler(), [$this->request, $parameters->getBag()]);
                break;
            case Route::CONTROLLER:
                $this->controller = $route->getHandler()->getController();
                $this->action = $route->getHandler()->getAction();
                $this->load();
                break;
        }

        App::getInstance()->finish($this->response);
    }

    private function load(){

        try{
            $controller = $this->container->resolve($this->container->get(Helper::class)->getControllerNs($this->controller));

            if(!method_exists($controller, $this->action)){
                throw new \Exception("{$this->action} does not exists in $this->controller");
            }
            $action = $this->action;
        }
        catch (\Exception $e){
            $controller = $this->container->resolve($this->container->get(Helper::class)->getControllerNs('index'));
            $action = 'error404';
        }

        $this->response = $controller->{$action}($this->request, $this->parameters);

    }



}