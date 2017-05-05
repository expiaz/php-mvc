<?php

namespace Core\Factory;

use Core\Utils\HttpParameterBag;
use Core\Http\Request;

class RequestFactory extends Factory {

    public function create(HttpParameterBag $parameters){
        return new Request($parameters, new HttpParameterBag(array_slice($_GET, 1)), new HttpParameterBag($_POST), new HttpParameterBag($_FILES), new HttpParameterBag($_COOKIE));
    }

}