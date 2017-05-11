<?php

namespace App\Controller;

use Core\Form\Field;
use Core\Form\Form;
use Core\Http\Request;
use Core\Mvc\Controller\Controller;
use Core\Mvc\View\View;
use Core\Utils\HttpParameterBag;

class IndexController extends Controller{

    public function index(Request $r, HttpParameterBag $p){
        echo 'index';
    }

    public function allget(Request $r, HttpParameterBag $p){
        echo 'allget';
    }

    public function allpost(Request $r, HttpParameterBag $p){
        echo 'allpost';
    }

    public function default(Request $r, HttpParameterBag $p){
        echo 'this is the default page';
    }

    public function id(Request $r, HttpParameterBag $p){
        return View::render('index', [
            'content' => $p['id']
        ]);
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
    }

    public function error404(Request $r, HttpParameterBag $p){
        return View::render('error/404', [
            'error' => true,
            'message' => 'Bad request'
        ]);
    }

}