<?php

use Core\Facade\Contracts\RouterFacade;


//create schema
RouterFacade::get('/create_tables', 'index@create');
RouterFacade::get('/abc', 'index@index');


//auth
RouterFacade::get('/auth', 'user@auth')
    ->use('user@alreadyCoMiddleware');
RouterFacade::post('/auth', 'user@auth')
    ->use('user@alreadyCoMiddleware');

//projets
RouterFacade::get('*', 'convention@all')
    ->use('user@middleware');

RouterFacade::get('/projets', 'convention@all')
    ->use('user@middleware');

RouterFacade::get('/projets/:id', 'convention@single')
    ->use('user@middleware')
    ->use('convention@ensureExists');

RouterFacade::get('/projets/add', 'convention@add')
    ->use('user@middleware')
    ->use('user@admin');

RouterFacade::post('/projets/add', 'convention@add')
    ->use('user@middleware')
    ->use('user@admin');

RouterFacade::get('/projets/edit/:id', 'convention@edit')
    ->use('user@middleware')
    ->use('user@admin')
    ->use('convention@ensureExists');

RouterFacade::post('/projets/edit/:id', 'convention@edit')
    ->use('user@middleware')
    ->use('user@admin')
    ->use('convention@ensureExists');

//deco
RouterFacade::get('/deco', 'user@deco')
    ->use('user@middleware');