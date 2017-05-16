<?php

namespace Core\Http;

class Request extends AbstractRequest {

    public static function buildFromScheme(RequestScheme $scheme){
        return new static($scheme->getParameters(), $scheme->getGet(), $scheme->getPost(), $scheme->getFiles(), $scheme->getCookies(), $scheme->getHeaders(), $scheme->getBody(), $scheme->getMethod());
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