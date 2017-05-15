<?php


use Core\Facade\Contracts\RouterFacade;
use Core\Facade\Contracts\UrlFacade;

RouterFacade::get('/', 'index@index');

RouterFacade::post('/', 'index@post');

RouterFacade::get('/redirect-me', function ($r, $p){
    RouterFacade::redirect(UrlFacade::create('/index', ['a' => 2]));
});


