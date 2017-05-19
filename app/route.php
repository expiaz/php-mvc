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

//sub
RouterFacade::get('/subscribe', 'user@subscribe')
    ->use('user@alreadyCoMiddleware');
RouterFacade::post('/subscribe', 'user@subscribe')
    ->use('user@alreadyCoMiddleware');

/*
 * FILMS
 */
RouterFacade::get('/', 'film@all')
    ->use('user@middleware');
RouterFacade::get('/film', 'film@all')
    ->use('user@middleware');
//profile
RouterFacade::get('/film/:id', 'film@profile')
    ->use('user@middleware')
    ->use('film@ensureExistsMiddleware');

RouterFacade::get('/film/edit/:id', 'film@edit')
    ->use('user@middleware')
    ->use('film@ensureExistsMiddleware');
RouterFacade::post('/film/edit/:id', 'film@edit')
    ->use('user@middleware')
    ->use('film@ensureExistsMiddleware');

RouterFacade::get('/film/add', 'film@add')
    ->use('user@middleware');
RouterFacade::post('/film/add', 'film@add')
    ->use('user@middleware');

/*
 * ACTEURS
 */
RouterFacade::get('/acteur', 'acteur@all')
    ->use('user@middleware');

RouterFacade::get('/acteur/:id', 'acteur@profile')
    ->use('user@middleware')
    ->use('acteur@ensureExistsMiddleware');

RouterFacade::get('/acteur/edit/:id', 'acteur@edit')
    ->use('user@middleware')
    ->use('acteur@ensureExistsMiddleware');
RouterFacade::post('/acteur/edit/:id', 'acteur@edit')
    ->use('user@middleware')
    ->use('acteur@ensureExistsMiddleware');

RouterFacade::get('/acteur/add', 'acteur@add')
    ->use('user@middleware');
RouterFacade::post('/acteur/add', 'acteur@add')
    ->use('user@middleware');


/*
 * REALISATEURS
 */
RouterFacade::get('/realisateur', 'realisateur@all')
    ->use('user@middleware');

RouterFacade::get('/realisateur/:id', 'realisateur@profile')
    ->use('user@middleware')
    ->use('realisateur@ensureExistsMiddleware');

RouterFacade::get('/realisateur/edit/:id', 'realisateur@edit')
    ->use('user@middleware')
    ->use('realisateur@ensureExistsMiddleware');
RouterFacade::post('/realisateur/edit/:id', 'realisateur@edit')
    ->use('user@middleware')
    ->use('realisateur@ensureExistsMiddleware');

RouterFacade::get('/realisateur/add', 'realisateur@add')
    ->use('user@middleware');
RouterFacade::post('/realisateur/add', 'realisateur@add')
    ->use('user@middleware');