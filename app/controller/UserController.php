<?php

namespace App\Controller;

use Core\Form\Form;
use Core\Http\Query;
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
                'user' => $u,
                'updatePath' => Query::build(Query::getController(), 'update', $u->id)
            ]);
        }
        return View::render('/user/profile', [
            'error' => 'no user found'
        ]);
    }

    public function update($p, $h){
        $m = $this->getModel();
        if($h['POST']){
            $user = $m->getById($h['POST']['id']);
            $user->setPseudo($h['POST']['pseudo']);
            $user->setMail($h['POST']['mail']);
            $m->update($user);
            return Router::redirect([
                'controller' => 'user',
                'action' => 'profile',
                'param' => $user->id
            ]);
        }
        $u = $m->getById($p['id']);
        return View::render('user/update', [
            'user' => $u,
            'form' => Form::build($u,[
                'method' => 'POST'
            ])
        ]);
    }

}