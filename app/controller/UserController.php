<?php

namespace App\Controller;

use Core\Http\Router;
use Core\Http\Session;
use Core\Mvc\Controller\Controller;
use Core\Mvc\View\View;

class UserController extends Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function index($p,$h){
        $id = Session::get('connected');
        if($id){
            return View::render('/user/profile', [
                'user' => $this->getModel()->getById($id)
            ]);
        }
        return View::render('/user/index', [
            'users' => $this->getModel()->getAll()
        ]);
    }

    public function profile($p,$h){
        $id = $p['id'] ?? $p[0];
        $u = $this->getModel()->getById($id);
        if($u){
            return View::render('/user/profile', [
                'user' => $u
            ]);
        }
        return View::render('/user/profile', [
            'error' => 'no user found'
        ]);
    }

}