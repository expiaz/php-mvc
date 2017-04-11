<?php

namespace App\Controller;

use Core\Database\Orm\Schema\Table;
use Core\Mvc\Controller\Controller;
use Core\Mvc\View\View;

class IndexController extends Controller{

    public function __construct(){
        parent::__construct();
    }

    public function index(){

        $table = new Table('index');
        $table->prefix('keyroes');
        $table->addField('id')
            ->autoIncrement();
        $table->addField('acteur')
            ->type('varchar')
            ->length('255')
            ->oneToOne('acteur','id');

        $t =  implode("\n\n",$table->describe());

        return View::render('index', [
            'content' => $t
        ]);
    }

}