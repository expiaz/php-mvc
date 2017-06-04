<?php

namespace App\Controller;

use App\Schema\AccompteProjetSchema;
use App\Schema\AccompteSchema;
use App\Schema\ActeurSchema;
use App\Schema\ConventionSchema;
use App\Schema\EntrepriseSchema;
use App\Schema\EtudeSchema;
use App\Schema\EtudiantSchema;
use App\Schema\FilmSchema;
use App\Schema\FraisProjetSchema;
use App\Schema\FraisSchema;
use App\Schema\RealisateurSchema;
use App\Schema\UserSchema;
use Core\Database\Database;
use Core\Database\Orm\ORM;
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
        $out = implode("\n", array_map(function($e){
            return implode("\n", $this->container[$e]->statement());
        }, [
            UserSchema::class,
            EntrepriseSchema::class,
            EtudiantSchema::class,
            FraisSchema::class,
            AccompteSchema::class,
            FraisProjetSchema::class,
            AccompteProjetSchema::class,
            ConventionSchema::class,
            EtudeSchema::class
        ]));

        return \View::render('index', [
            'content' => $out
        ]);
    }

    public function create(Request $request, Response $response){

        ORM::transactionUp($this->container[Database::class],array_map(function($e){
            return $this->container[$e];
        }, [
            UserSchema::class,
            EntrepriseSchema::class,
            EtudiantSchema::class,
            FraisSchema::class,
            AccompteSchema::class,
            FraisProjetSchema::class,
            AccompteProjetSchema::class,
            ConventionSchema::class,
            EtudeSchema::class
        ]));

        return \View::render('index', [
            'content' => ''
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