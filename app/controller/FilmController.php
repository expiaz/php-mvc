<?php

namespace App\Controller;

use App\Entity\FilmEntity;
use Core\Form\Field;
use Core\Form\FormBuilder;
use Core\Http\Request;
use Core\Http\Router;
use Core\Mvc\Controller\Controller;
use Core\Mvc\View\View;

class FilmController extends Controller{

    public function __construct(){
        parent::__construct();
    }

    public function index(Request $request, ...$parameters){
        return View::render('film/index', [
            'films' => $this->getModel()->getAll()
        ]);
    }

    public function add(Request $r, $id){

        $film = isset($id) ? $this->getModel()->getById($id) : new FilmEntity();

        if(!$film)
            return Router::redirect([
                'controller' => 'film'
            ]);

        $form = FormBuilder::buildFromEntity($film)
            ->field((new Field())
                ->type('submit')
                ->value('modifier'));

        $form->handleRequest($r);

        if($form->isSubmitted()){
            $film = $form->getData();
            $film->persist();
            return Router::redirect([
                'controller' => 'film',
                'action' => 'index'
            ]);
        }

        return View::render('film/add', [
            'form' => $form->build()
        ]);
    }

    public function profile($r, $id){
        $film = $this->getModel()->getById($id);
        if(!$film)
            return Router::redirect([
                'controller' => 'film'
            ]);
        return View::render('film/profile', [
            'film' => $film
        ]);
    }

}