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

    public function __construct(Container $c, string $viewPath)
    {
        $this->container = $c;

        $path = VIEW . str_replace('/', DS,trim($viewPath,'/')) . '.php';
        if (! file_exists($path)) {
            $path = VIEW . 'index.php';
        }
        $this->path = $path;
    }

    function render(array $vars)
    {
        if(!isset($vars['error'])){
            $vars['error'] = false;
        }
        if(!isset($vars['title'])){
            $vars['title'] = \Request::getUrl();
        }

        $vars['connected'] = [
            'link' => $this->container[Session::class]->exists('connected_as') ? (new Url('/deco'))->build() : (new Url('/auth'))->build(),
            'message' => $this->container[Session::class]->exists('connected_as') ? 'deconnexion' : 'connexion'
        ];

        $vars['home'] = WEBROOT;

        return $this->capture($vars);
    }

    private function capture(array &$vars): string{
        $level = ob_get_level();
        ob_start();
        extract($vars, EXTR_SKIP);
        require_once LAYOUT . 'header.php';
        require_once($this->path);
        require_once LAYOUT . 'footer.php';
        $content = ob_get_clean();
        $newLevel = ob_get_level();
        if($newLevel !== $level){
            throw new \Exception("[View::render] ob_level fails expected $level got {$newLevel}");
        }
        return $content;
    }

}