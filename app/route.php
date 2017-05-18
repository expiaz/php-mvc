<?php

use Core\Facade\Contracts\RouterFacade;


//auth
RouterFacade::get('/auth', 'user@auth')
    ->use('user@alreadyCoMiddleware');
RouterFacade::post('/auth', 'user@auth')
    ->use('user@alreadyCoMiddleware');

//sub
RouterFacade::get('/subscribe', 'user@subscribe')
    ->use('user@alreadyCoMiddleware');
RouterFacade::post('/subscribe', 'user@subscribe')
    ->use('user@alreadyCoMiddleware');

//films
RouterFacade::get('/', 'film@all')
    ->use('user@middleware');
RouterFacade::get('/film', 'film@all')
    ->use('user@middleware');
//profile
RouterFacade::get('/film/:id', 'film@profile')
    ->use('user@middleware');







