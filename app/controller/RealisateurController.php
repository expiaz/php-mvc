<?php

namespace App\Controller;

use Core\Mvc\Controller\Controller;
use Core\Mvc\View\View;

class RealisateurController extends Controller{

    public function __construct(){
        parent::__construct();
    }

    public function index($http, ...$parameters){
        return View::render('realisateur/index', [
            'realisateurs' => $this->getModel()->getAll()
        ]);
    }

    public function profile($r, $id){
        $realisateur = $this->getModel()->getById($id);
        if(!$realisateur)
            return Router::redirect([
                'controller' => 'realisateur'
            ]);
        return View::render('realisateur/profile', [
            'realisateur' => $realisateur
        ]);
    }

}