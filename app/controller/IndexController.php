<?php

namespace App\Controller;

use App\Schema\ActeurSchema;
use App\Schema\FilmSchema;
use App\Schema\RealisateurSchema;
use App\Schema\UserSchema;
use Core\Exception\SqlAlterException;
use Core\Facade\Contracts\FormFacade;
use Core\Form\Field\Input\PasswordInput;
use Core\Form\Field\Input\SubmitInput;
use Core\Form\Field\Input\TextInput;
use Core\Form\Form;
use Core\Http\Request;
use Core\Http\Response;
use Core\Mvc\Controller\Controller;

class IndexController extends Controller{

    public function index(Request $request, Response $response){
        return \View::render('index', [
            'content' => 'index'
        ]);
    }

    public function error404(Request $request, Response $response){
        $response->withStatus(404);
        return \View::render('error/404', [
            'error' => '404 Not found',
            'message' => "bad request, object not found for \"{$request->getUrl()}\""
        ]);
    }

}