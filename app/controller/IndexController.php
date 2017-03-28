<?php
namespace App\Controller;

use Core\Mvc\Controller\Controller;
use Core\Mvc\View\View;

class IndexController extends Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function index($http, ...$parameters){
        View::render('index', []);
    }


}