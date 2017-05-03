<?php

namespace App\Controller;

use Core\Form\FormBuilder;
use Core\Http\Request;
use Core\Mvc\Controller\Controller;
use Core\Mvc\View\View;

class IndexController extends Controller{

    public function index(Request $r, ... $p){

        echo 'hi';

    }

    public function a(Request $r){
        $f = $this->container[FormBuilder::class]->build($this->getRepository()->getModel());
        View::render('index', [
            'content' => $f->build()
        ]);
    }

}