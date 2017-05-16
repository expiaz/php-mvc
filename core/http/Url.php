<?php
namespace Core\Http;

final class Url{

    private $controller;
    private $action;
    private $payload;
    private $url;
    private $destination;

    public function __construct(string $destination = '', array $payload = [])
    {
        /*$this->controller = $controller;
        $this->action = $action;*/
        $this->destination = ltrim($destination, '/');
        $this->payload = $payload;
    }

    /*public function setController($controller){
        $this->controller = $controller;
    }

    public function setAction($action){
        $this->action = $action;
    }*/

    public function setPayload($payload = []){
        $this->payload = $payload;
    }

    public function setUrl($url){
        $this->url = $url;
    }


    /*public function getController(){
        return $this->controller;
    }

    public function getAction(){
        return static::$_action;
    }*/

    public function getPayload(){
        return $this->payload;
    }

    public function getUrl(){
        return $this->url;
    }

    public function build(){
        return $this->url = WEBROOT . $this->destination . (count($this->payload) ? '&' . http_build_query($this->payload) : '');
    }

    public function __toString()
    {
        return $this->build();
    }


}