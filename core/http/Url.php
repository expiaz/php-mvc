<?php
namespace Core\Http;

final class Url{

    private $controller;
    private $action;
    private $payload;
    private $url;

    public function __construct($controller = 'index', $action = 'index', $payload = [])
    {
        $this->controller = $controller;
        $this->action = $action;
        $this->payload = $payload;
    }



    public function setController($controller){
        $this->controller = $controller;
    }

    public function setAction($action){
        $this->action = $action;
    }

    public function setPayload($payload = []){
        $this->payload = $payload;
    }

    public function setUrl($url){
        $this->url = $url;
    }


    public function getController(){
        return $this->controller;
    }

    public function getAction(){
        return static::$_action;
    }

    public function getPayload(){
        return $this->payload;
    }

    public function getUrl(){
        return $this->url;
    }

    public function build(){
        return $this->url = WEBROOT . $this->controller . '/' . $this->action . (count($this->payload) ? '&' . http_build_query($this->payload) : '');
    }


}