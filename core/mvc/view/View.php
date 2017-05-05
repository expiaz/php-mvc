<?php

namespace Core\Mvc\View;

use Core\Database\Database;
use Core\Http\Query;
use Core\Http\Session;
use Core\Http\Url;

/**
 * Class View
 * @package Core\Mvc\View
 */

/*
 * TODO DI with container, request (composition?), viewPath, parameterBag
 *
 */

abstract class View
{

    static function render($viewPath, $vars)
    {

        $container = container();

        $path = VIEW . trim($viewPath,'/') . '.php';
        if (!file_exists($path)) {
            $path = VIEW . 'index.php';
        }
        if(!isset($vars['error'])){
            $vars['error'] = false;
        }
        if(!isset($vars['title'])){
            $vars['title'] = 'title';
        }

        $vars['connected'] = [
            'link' => $container[Session::class]->exists('connected') ? (new Url('index', 'deconnexion'))->build() : (new Url('index', 'deconnexion'))->build(),
            'message' => $container[Session::class]->exists('connected') ? 'deconnexion' : 'connexion'
        ];

        $vars['connection_link'] = "<a href=\" {$vars['connected']['link']} \">{$vars['connected']['message']}</a>";

        $vars['home'] = WEBROOT;

        echo static::capture($path,$vars);

        static::end();
    }

    private static function capture($viewPath, $vars){
        ob_start();
        extract($vars, EXTR_SKIP);
        require_once LAYOUT . 'header.php';
        require_once($viewPath);
        require_once LAYOUT . 'footer.php';
        return ob_get_clean();
    }

    private static function end(){
        exit(0);
    }

}