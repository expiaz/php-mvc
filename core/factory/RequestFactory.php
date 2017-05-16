<?php

namespace Core\Factory;

use Core\Http\Route\Route;
use Core\Utils\HttpParameterBag;
use Core\Http\Request;

class RequestFactory extends Factory {

    public function create(HttpParameterBag $parameters, Route $route){

        if(is_callable('getallheaders')){
            $headers = getallheaders();
        }
        else{
            $headers = \http_get_request_headers();
        }

        if(is_callable('http_get_request_body')){
            try{
                $body = \http_get_request_body();
            }
            catch (\Exception $e){
                $body = '';
            }
        }
        else{
            $body = '';
        }


        return new Request($parameters, new HttpParameterBag($_GET), new HttpParameterBag($_POST), new HttpParameterBag($_FILES), new HttpParameterBag($_COOKIE), $headers, $body, $route);
    }

}