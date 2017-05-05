<?php

namespace Core\App;


use Core\Factory\RequestFactory;
use Core\Helper;
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

    private $container;

    public function __construct(Container $container, Route $route, HttpParameterBag $parameters)
    {
        $this->container = $container;

        $this->parameters = $parameters;

        $this->request = $container[RequestFactory::class]->create($parameters);

        switch ($route->getType()){
            case Route::CLOSURE:
                call_user_func_array($route->getHandler(), [$this->request, $parameters->getBag()]);
                break;
            case Route::CONTROLLER:
                $this->controller = $route->getHandler()->getController();
                $this->action = $route->getHandler()->getAction();
                break;
        }

        $this->load();
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

        $controller->{$action}($this->request, $this->parameters);

    }



}