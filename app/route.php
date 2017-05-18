<?php


use Core\Facade\Contracts\RouterFacade;

RouterFacade::get('/', 'index@index');
RouterFacade::post('/', 'index@index');

RouterFacade::get('/show/:id', 'index@show');
RouterFacade::post('/show/:id', 'index@show');

RouterFacade::get('/auth', 'index@auth');
RouterFacade::post('/auth', 'index@auth');


