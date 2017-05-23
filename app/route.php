<?php

use Core\Facade\Contracts\RouterFacade;

//create schema
RouterFacade::get('/create_tables', 'index@create');


//auth
RouterFacade::get('/auth', 'user@auth')
    ->use('user@alreadyCoMiddleware');
RouterFacade::post('/auth', 'user@auth')
    ->use('user@alreadyCoMiddleware');

//deco
RouterFacade::get('/deco', 'user@deco')
    ->use('user@middleware');