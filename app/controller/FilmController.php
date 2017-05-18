<?php

namespace App\Controller;

use Core\Http\Request;
use Core\Http\Response;
use Core\Mvc\Controller\Controller;

class FilmController extends Controller{

    public function index(Request $request, Response $response){

    }

    public function all(Request $request, Response $response){
        $films = $this->getRepository()->getAll();
        return \View::render('film/index', [
            'films' => $films
        ]);
    }

}