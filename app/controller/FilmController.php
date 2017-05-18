<?php

namespace App\Controller;

use Core\Exception\NoDataFoundException;
use Core\Facade\Contracts\RouterFacade;
use Core\Facade\Contracts\UrlFacade;
use Core\Facade\Contracts\ViewFacade;
use Core\Http\Request;
use Core\Http\Response;
use Core\Mvc\Controller\Controller;

class FilmController extends Controller{

    public function index(Request $request, Response $response){

    }

    public function all(Request $request, Response $response){
        try{
            $films = $this->getRepository()->getAll();
        } catch (NoDataFoundException $e){
            $films = [];
        }

        return ViewFacade::render('film/index', [
            'films' => $films
        ]);
    }

    public function profile(Request $request, Response $response){
        try{
            $film = $this->getRepository()->getById($request->getParameter('id'));
        } catch (NoDataFoundException $e){
            $response->withStatus(404);
            return ViewFacade::render('error/404', [
                'error' => '404 Not Found',
                'message' => "No film for id {$request->getParameter('id')}"
            ]);
        }

        return ViewFacade::render('film/profile', [
            'film' => $film
        ]);
    }

}