<?php

namespace Core\Http;

use Core\Utils\HttpParameterBag;

class Request{

    private $parameters;
    private $get;
    private $post;
    private $files;
    private $cookies;

    public function __construct(HttpParameterBag $parameters, HttpParameterBag $get, HttpParameterBag $post, HttpParameterBag $files, HttpParameterBag $cookie)
    {
        $this->parameters = $parameters;
        $this->get = $get;
        $this->post = $post;
        $this->files = $files;
        $this->cookies = $cookie;
    }

    public function getParameter($prop = null){
        if(is_null($prop))
            return $this->parameters;
        return $this->parameters[$prop];
    }


    public function getPost($prop = null){
        if(is_null($prop))
            return $this->post;
        return $this->post[$prop];
    }

    public function getGet($prop = null){
        if(is_null($prop))
            return $this->get;
        return $this->get[$prop];
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