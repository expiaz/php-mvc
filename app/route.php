<?php

use Core\Facade\Contracts\RouterFacade;

//default
RouterFacade::on('*', 'film@all')
    ->use('user@middleware');;

//auth
RouterFacade::get('/auth', 'user@auth');
RouterFacade::post('/auth', 'user@auth');

//films
RouterFacade::get('/', 'film@all')
    ->use('user@middleware');




