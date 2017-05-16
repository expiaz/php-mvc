<?php

namespace Core\Http;

use Core\Http\Interfaces\RequestInterface;
use Core\Http\Route\Route;
use Core\Utils\HttpParameterBag;

class AbstractRequest implements RequestInterface{

    protected $parameters;
    protected $get;
    protected $post;
    protected $files;
    protected $cookies;
    protected $headers;
    protected $body;
    protected $method;
    protected $route;
    protected $url;

    const ALL = 'ALL';
    const GET = 'GET';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const POST = 'POST';

    public function __construct(HttpParameterBag $parameters, HttpParameterBag $get, HttpParameterBag $post, HttpParameterBag $files, HttpParameterBag $cookie, array $headers, string $body, Route $route)
    {
        $this->parameters = $parameters;
        $this->get = $get;
        $this->post = $post;
        $this->files = $files;
        $this->cookies = $cookie;
        $this->headers = $headers;
        $this->body = $body;
        $this->route = $route;
        $this->url = $route->getUrl();
        $this->method = $route->getMethod();
    }

    public function getRoute(){
        return $this->route;
    }

    public function getUrl(){
        return $this->url;
    }

    public function getMethod(){
        return $this->method;
    }

    public function getParameter($prop = null){
        if(is_null($prop))
            return $this->parameters;
        return $this->parameters[$prop];
    }

    public function getHeader($prop = null){
        if(is_null($prop))
            return $this->headers;
        return $this->headers[$prop];
    }

    public function getBody(){
        return $this->body;
    }

    public function getPost($prop = null){
        if(is_null($prop))
            return $this->post;
        return $this->post[$prop];
    }

    public function getParsedBody($prop = null){
        return $this->getPost($prop);
    }

    public function getGet($prop = null){
        if(is_null($prop))
            return $this->get;
        return $this->get[$prop];
    }

    public function getQueryString($prop = null){
        return $this->getGet($prop);
    }

    public function getFiles($prop = null){
        if(is_null($prop))
            return $this->files;
        return $this->files[$prop];
    }

    public function getCookies($prop = null){
        if(is_null($prop))
            return $this->cookies;
        return $this->cookies[$prop];
    }

}