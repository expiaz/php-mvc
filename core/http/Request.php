<?php

namespace Core\Http;

class Request{

    private $get;
    private $post;

    public function __construct($get, $post)
    {
        $this->get = $this->secureHttpArguments($get);
        $this->post = $this->secureHttpArguments($post);
    }

    public function getPost($prop = null){
        if(is_null($prop))
            return $this->post;
        return $this->post[$prop] ?? null;
    }

    public function getGet($prop = null){
        if(is_null($prop))
            return $this->get;
        return $this->get[$prop] ?? null;
    }

    private function secureHttpArguments($http){
        if(is_array($http)){
            $ret = [];
            foreach ($http as $k => $h) {
                $ret[$k] = is_array($h) ? $this->secureHttpArguments($h) : htmlspecialchars($h);
            }
            return $ret;
        }
        return htmlspecialchars($http);
    }

}