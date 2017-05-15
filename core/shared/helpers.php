<?php

if(! function_exists('app')){

    function app($service = null){
        if(is_null($service))
            return \Core\App::getInstance();
        return \Core\App::make($service);
    }

}

if(! function_exists('container')){

    function container($service = null){
        if(is_null($service))
            return \Core\App::getInstance()->getContainer();
        return \Core\App::make($service);
    }

}

if(! function_exists('view')){

    function view(string $viewPath, array $args = []){
        return (\Core\App::make(\Core\Mvc\View\View::class))->render($viewPath, $args);
    }

}