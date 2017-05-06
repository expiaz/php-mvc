<?php

namespace App\Controller;

use Core\Facade\Contracts\FormFacade;
use Core\Facade\Contracts\RequestFacade;
use Core\Facade\Contracts\UrlFacade;
use Core\Form\FormBuilder;
use Core\Http\Request;
use Core\Mvc\Controller\Controller;
use Core\Mvc\View\View;
use Core\Utils\HttpParameterBag;

class IndexController extends Controller{

    public function index(Request $r, HttpParameterBag $p){
        $f = FormFacade::create($this->getRepository()->getModel());
        return View::render('index', [
            'content' => $f
        ]);
    }

    public function default(Request $r, HttpParameterBag $p){
        echo 'this is the default page';
    }

    public function id(Request $r, HttpParameterBag $p){
        echo $p->getId();
    }

    public function error404(Request $r, HttpParameterBag $p){
        return View::render('error/404', [
            'error' => true,
            'message' => 'Bad request'
        ]);
    }

}