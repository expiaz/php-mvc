<?php

namespace Core\Http;

use Core\Utils\HttpParameterBag;

class Request{

    private $parameters;
    private $get;
    private $post;
    private $files;
    private $cookies;
    private $headers;
    private $body;
    private $method;

    const ALL = 'ALL';
    const GET = 'GET';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const POST = 'POST';

    public function __construct(HttpParameterBag $parameters, HttpParameterBag $get, HttpParameterBag $post, HttpParameterBag $files, HttpParameterBag $cookie, array $headers, string $body, string $method)
    {
        $this->parameters = $parameters;
        $this->get = $get;
        $this->post = $post;
        $this->files = $files;
        $this->cookies = $cookie;
        $this->headers = $headers;
        $this->body = $body;
        $this->method = $method;
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

    public function isGet(){
        return $this->method === static::GET;
    }

    public function isPost(){
        return $this->method === static::POST;
    }

    public function isPut(){
        return $this->method === static::PUT;
    }

    public function isDelete(){
        return $this->method === static::DELETE;
    }

    public function isXhr(){
        return isset($this->headers['X-Requested-With']) && $this->headers['X-Requested-With'] === 'XMLHttpRequest';
    }

}