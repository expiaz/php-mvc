<?php

namespace App\Controller;

use Core\Form\FormBuilder;
use Core\Http\Request;
use Core\Mvc\Controller\Controller;
use Core\Mvc\View\View;

class IndexController extends Controller{

    public function __construct(){
        parent::__construct();
    }

    public function index(Request $r, ... $p){

        $form = FormBuilder::build($this->getRepository()->getModel());

        return View::render('index', [
            'content' => $form->build()
        ]);

    }

}