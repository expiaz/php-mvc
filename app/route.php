<?php


use Core\Facade\Contracts\RouterFacade;
use Core\Form\Field;
use Core\Form\Form;

/*
RouterFacade::use(function ($next){
    if(! \Session::exists('connected')){
        $f = new Form();
        $f->field((new Field())->name('login')->required()->placeholder('login')->type('text'));
        $f->field((new Field())->name('password')->required()->placeholder('password')->type('password'));
        $f->field((new Field())->name('submit')->type('submit')->value('envoyer'));
        return \View::render('auth', [
            'authForm' => $f
        ]);
    }
    return $next();
});
*/


RouterFacade::get('/', 'index@index')
    ->use('index@authMiddleware');

RouterFacade::get('/auth', 'index@auth');
RouterFacade::post('/auth', 'index@auth');


