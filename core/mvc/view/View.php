<?php

namespace Core\Mvc\View;

use Core\Http\Query;
use Core\Http\Session;

abstract class View
{

    static function render($viewPath, $vars)
    {
        $path = VIEW . trim($viewPath,'/') . '.php';
        if (!file_exists($path)) {
            $path = VIEW . 'index.php';
        }
        if(!isset($vars['error'])){
            $vars['error'] = false;
        }

        $vars['connected'] = [
            'link' => Session::get('connected') ? Query::build('index', 'deconnexion') : Query::build('index', 'connexion'),
            'message' => Session::get('connected') ? 'deconnexion' : 'connexion',
        ];

        $vars['title'] = Query::getAction();

        $vars['home'] = WEBROOT;
        echo self::capture($path,$vars);
        return;
    }

    private static function capture($viewPath,$vars){
        ob_start();
        extract($vars, EXTR_SKIP);
        require_once LAYOUT . 'header.php';
        require_once($viewPath);
        require_once LAYOUT . 'footer.php';
        return ob_get_clean();
    }

}