<?php

if(! function_exists('app')){

    function app($service = null){
        if(is_null($service))
            return \Core\App::getInstance();
        return \Core\App::getInstance()->make($service);
    }

}

if(! function_exists('container')){

    function container($service = null){
        if(is_null($service))
            return \Core\App::getInstance()->getContainer();
        return \Core\App::getInstance()->getContainer()->get($service);
    }

}