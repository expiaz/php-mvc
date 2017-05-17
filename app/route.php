<?php


use Core\Facade\Contracts\RouterFacade;

RouterFacade::get('/', 'index@index');
RouterFacade::post('/', 'index@index');

RouterFacade::get('/auth', 'index@auth');
RouterFacade::post('/auth', 'index@auth');


