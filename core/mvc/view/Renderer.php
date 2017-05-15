<?php

namespace Core\Mvc\View;

use Core\App\Container;
use Core\App\ObBuffer;
use Core\Http\Response;

class Renderer{

    //TODO Handle response and headers + content length etc

    public function __construct(Container $c, $body = null)
    {
        $this->response = container(Response::class);
        if(is_null($this->response->getBody()) && ! is_null($body)){
            $this->response->withBody((string) $body);
        }

        $this->output();
    }

    private function output(){
        $debug = container(ObBuffer::class)->unbufferize();
        if(DEV){
            $this->response->appendBody($debug);
        }
        if(! headers_sent()){
            header("HTTP/{$this->response->getHttpVersion()} {$this->response->getStatusCode()} {$this->response->getStatusPhrase()}");

            foreach ($this->response->getHeaders() as $header => $phrase){
                header("$header: $phrase", true);
            }

            echo $this->response->getBody();
        }
        $this->end();
    }

    private function end(){
        exit(0);
    }

}