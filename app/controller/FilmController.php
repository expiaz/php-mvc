<?php

namespace App\Controller;

use Core\Mvc\Controller\Controller;
use Core\Mvc\View\View;

class FilmController extends Controller{

    public function __construct(){
        parent::__construct();
    }

    public function index($http, ...$parameters){
        if($http['POST']){

        }
        else View::render('index', [
            'film' => $this->getModel()->getById(1)
        ]);
    }

}