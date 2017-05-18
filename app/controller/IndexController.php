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
        /*
        $sample = $this->getRepository()->getModel();

        $form = FormFacade::create($sample);

        $form->handleRequest($request);

        if($form->isSubmitted()){
            $hydrated = $form->getData();
            try{
                $this->getRepository()->insert($hydrated);
            } catch (SqlAlterException $e){
                return $e->getMessage();
            }
        }
        */

        return \View::render('index', [
            'content' => 'index'
        ]);
    }

    public function schema(Request $request, Response $response){
        $this->container[UserSchema::class]->up();
        $this->container[ActeurSchema::class]->up();
        $this->container[RealisateurSchema::class]->up();
        $this->container[FilmSchema::class]->up();

        return \View::render('index', [
            'content' => 1
        ]);
    }



    public function authMiddleware(Request $request, Response $response, callable $next){
        if(! \Session::exists('connected_as'))
            return \Router::redirect(\Url::create('/auth'));
        return $next($request, $response);
    }


    public function auth(Request $r){
        $f = new Form();

        $f->action(\Url::create('/auth'));

        $f->field((new TextInput())
            ->name('login')
            ->required()
            ->placeholder('login'));

        $f->field((new PasswordInput())
            ->name('password')
            ->required()
            ->placeholder('password'));

        $f->field((new SubmitInput())
            ->name('submit')
            ->value('envoyer'));

        $f->handleRequest($r);

        if($f->isSubmitted()){
            \Session::set('connected', true);
            return \Router::redirect(\Url::create('/'));
        }

        return \View::render('auth', [
            'authForm' => $f
        ]);
    }

    /*
    public function default(Request $r, HttpParameterBag $p){
        echo 'this is the default page';
    }

    public function getProject(Request $r, HttpParameterBag $p){
        if(! \Auth::isAuth()){
            return \View::render('login', [
                'error' => true,
                'message' => 'You need to be authenticated to access this page'
            ]);
        }

        if(! isset($p['id'])){
            return \Router::redirect(\Url::create('project', 'all'));
        }

        try{
            $project = $this->getRepository()->getById($p['id']);
            return \View::render('project/single', [
                'project' => $project
            ]);
        }
        catch(NoDataFoundException $e){
            return \View::render('error\404', [
                'error' => true,
                'message' => "Project of id {$p['id']} does not exists"
            ]);
        }

    }

    public function addProject(Request $r, HttpParameterBag $p){

        if(! \Auth::isAuth()){
            return View::render('login', [
                'error' => true,
                'message' => 'You need to be authenticated to access this page'
            ]);
        }

        $f = \Form::create($this->getRepository()->getModel());
        $f->handleRequest($r);

        if($f->isSubmitted()){
            if($this->getRepository()->insert($f->getData())){
                return View::render('admin/dashboard');
            }
            return View::render('error/500', [
                'error' => true,
                'message' => 'Problem while creating the project'
            ]);
        }

        return View::render('project/add', [
            'project' => $f
        ]);
    }*/

    public function show(Request $request, Response $response){
        $film = $this->getRepository()->getById($request->getParameter('id'));
        $form = FormFacade::create($film);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $hydrated = $form->getData();
            try{
                $this->getRepository()->update($hydrated);
            } catch (SqlAlterException $e){
                return $e->getMessage();
            }
        }

        return \View::render('index', [
            'content' => $form->build()
        ]);

    }

    public function error404(Request $request, Response $response){
        \Response::withStatus(404);
        return \View::render('error/404', [
            'error' => true,
            'message' => 'Bad request'
        ]);
    }

}