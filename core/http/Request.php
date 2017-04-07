<?php

namespace Core\Http;

class Request{

    private $get;
    private $post;
    private $files;

    public function __construct($get, $post, $files)
    {
        $this->get = $this->secureHttpArguments($get);
        $this->post = $this->secureHttpArguments($post);
        $this->post = $this->secureHttpArguments($files);
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

    public function getFiles($prop = null){
        if(is_null($prop))
            return $this->files;
        return $this->files[$prop] ?? null;
    }

    public function getPayload($prop = null, $type = null){
        if($type){
            switch($type){
                case 'post':
                    return $this->getPost($prop);
                case 'get':
                    return $this->getGet($prop);
                case 'file':
                    return $this->getFiles($prop);
            }
        }

        if($prop){
            return $this->getGet($prop) ?? $this->getPost($prop) ?? $this->getFiles($prop);
        }

        return [
            'get' => $this->get,
            'post' => $this->post,
            'files' => $this->files
        ];
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