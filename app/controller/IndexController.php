<?php

namespace App\Controller;

use Core\Form\Field;
use Core\Form\Form;
use Core\Http\Request;
use Core\Http\Response;
use Core\Mvc\Controller\Controller;

class IndexController extends Controller{

    public function index(Request $r, Response $response){
        return \View::render('index', [
            'content' => 'Welcome'
        ]);
    }

    public function authMiddleware(Request $request, Response $response, callable $next){
        if(! \Session::exists('connected'))
            return \Router::redirect(\Url::create('/auth'));
        return $next($request, $response);
    }


    public function auth(Request $r){
        $f = new Form();

        $f->field((new Field())
            ->name('login')
            ->required()
            ->placeholder('login')
            ->type('text'));

        $f->field((new Field())
            ->name('password')
            ->required()
            ->placeholder('password')
            ->type('password'));

        $f->field((new Field())
            ->name('submit')
            ->type('submit')
            ->value('envoyer'));

        $f->handleRequest($r);

        if($f->isSubmitted()){
            //validation logic
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

    public function error404(Request $request, Response $response){
        \Response::withStatus(404);
        return \View::render('error/404', [
            'error' => true,
            'message' => 'Bad request'
        ]);
    }

}