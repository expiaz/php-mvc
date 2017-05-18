<?php

namespace Core\Mvc\View;

use Core\App\Container;
use Core\Database\Database;
use Core\Http\Query;
use Core\Http\Request;
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

final class View
{

    public function __construct(Container $c)
    {
        $this->container = $c;
    }

    function render(string $viewPath, array $vars)
    {
        $path = VIEW . trim($viewPath,'/') . '.php';
        if (!file_exists($path)) {
            $path = VIEW . 'index.php';
        }
        if(!isset($vars['error'])){
            $vars['error'] = false;
        }
        if(!isset($vars['title'])){
            $vars['title'] = \Request::getUrl();
        }

        /*$vars['connected'] = [
            'link' => $this->container[Session::class]->exists('connected') ? (new Url('index', 'deconnexion'))->build() : (new Url('index', 'connexion'))->build(),
            'message' => $this->container[Session::class]->exists('connected') ? 'deconnexion' : 'connexion'
        ];

        $vars['connection_link'] = "<a href=\"{$vars['connected']['link']}\">{$vars['connected']['message']}</a>";*/

        $vars['home'] = WEBROOT;

        return $this->capture($path,$vars);
    }

    private function capture(string $viewPath, array &$vars){
        $level = ob_get_level();
        ob_start();
        extract($vars, EXTR_SKIP);
        require_once LAYOUT . 'header.php';
        require_once($viewPath);
        require_once LAYOUT . 'footer.php';
        $content = ob_get_clean();
        $newLevel = ob_get_level();
        if($newLevel !== $level){
            throw new \Exception("View::render ob_level fails expected $level got {$newLevel}");
        }
        return $content;
    }

}