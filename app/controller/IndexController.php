<?php

namespace App\Controller;

use App\Entity\RealisateurEntity;
use App\Entity\Schema\FilmSchema;
use Core\Http\Cookie;
use Core\Http\Query;
use Core\Mvc\Controller\Controller;
use Core\Mvc\View\View;

class IndexController extends Controller{

    public function __construct(){
        parent::__construct();
    }

    public function index(){

        return View::render('error/404', [
            'error' => 'index'
        ]);

    }

    public function click($http, ...$parameters){
        if(Cookie::exists('click')){
            if(Cookie::get('click') == "10")
                Cookie::set('click', 0);
            else
                Cookie::set('click', Cookie::get('click')+1);
        }
        else{
            Cookie::set('click', 0);
        }

        return View::render('click', [
            'clicks' => Cookie::get('click'),
            'refresh' => Query::build([
                'controller' => 'index',
                'action' => 'click'
            ])
        ]);
    }

}