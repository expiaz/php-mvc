<?php
namespace App\Controller;

use Core\Mvc\Controller\Controller;
use Core\Mvc\View\View;

class RealisateurController extends Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function index($param, $http){
        return View::render('realisateur/all', [
            'realisateurs' => $this->getModel()->getAll()
        ]);
    }

    public function profile($p,$h){
        $id = $p['id'] ?? $p[0] ?? null;
        $rea = $this->getModel()->getById($id);
        if($rea){
            return View::render('realisateur/profile', [
                'realisateur' => $rea
            ]);
        }
        return View::render('error/404', [
            'error' => 'no film found'
        ]);
    }


}