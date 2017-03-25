<?php
namespace App\Controller;

use Core\Mvc\Controller\Controller;
use Core\Mvc\View\View;

class FilmController extends Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function index($param, $http){
        return View::render('film/all', [
            'films' => $this->getModel()->getAll()
        ]);
    }

    public function profile($p,$h){
        $id = $p['id'] ?? $p[0] ?? null;
        $film = $this->getModel()->getById($id);
        if($film){
            return View::render('film/profile', [
                'film' => $film
            ]);
        }
        return View::render('error/404', [
            'error' => 'no film found'
        ]);
    }


}