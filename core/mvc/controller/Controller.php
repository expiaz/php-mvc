<?php
namespace Core\Mvc\Controller;

use Core\App\Container;
use Core\Http\Request;
use Core\Mvc\Repository\Repository;
use Core\Utils\HttpParameterBag;

abstract class Controller{

    protected $repository;
    protected $container;

    public function __construct(Container $container, Repository $repository)
    {
        $this->container = $container;
        $this->repository = $repository;
    }

    public function getRepository(): Repository{
        return $this->repository;
    }

    public function getContainer(): Container{
        return $this->container;
    }

    public abstract function index(Request $request, HttpParameterBag $parameters);

}