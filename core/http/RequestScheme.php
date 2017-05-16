<?php

namespace Core\Http;

class RequestScheme extends AbstractRequest {

    public function setParameters(array $parameters){
        $this->parameters = $parameters;
    }

    public function setHeaders(array $headers){
         $this->headers = $headers;
    }

    public function setBody($body){
        $this->body = $body;
    }

    public function setPost(array $post){
        $this->post = $post;
    }

    public function setGet(array $get){
        $this->get = $get;
    }

}