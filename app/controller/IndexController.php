<?php
namespace App\Controller;

use App\Model\UserModel;
use Core\Http\Router;
use Core\Http\Session;
use Core\Mvc\Controller\Controller;
use Core\Mvc\View\View;

class IndexController extends Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function index($param, $http){
        $co = Session::get('connected');
        if(!$co) {
            return Router::redirect([
                'controller' => 'index',
                'action' => 'connexion'
            ]);
        }
        $m = new UserModel();
        return View::render('index',[
            'location' => 'index',
            'message' => 'greeting ' . $m->getById($co)->pseudo
        ]);
    }

    public function connexion($p, $h){
        if($h['POST']){
            $auth = $this->getModel()->authenticate($h['POST']['login'], $h['POST']['pwd']);
            if(!$auth)
                return View::render('connexion', [
                    'error' => 'bad credentials',
                    'login' => $h['POST']['login']
                ]);
            Session::set('connected', $auth);
            return Router::redirect([
                'controller' => 'index',
                'action' => 'index'
            ]);
        }
        return View::render('connexion', []);
    }

    public function deconnexion($p, $h){
        $c = Session::get('connected');
        if($c){
            Session::delete('connected');
        }
        return Router::redirect([
            'controller' => 'index',
            'action' => 'index'
        ]);
    }


}