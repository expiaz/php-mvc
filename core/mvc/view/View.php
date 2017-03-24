<?php

namespace Core\Mvc\View;

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
        echo self::capture($path,$vars);
        return;
    }

    private static function capture($viewPath,$vars){
        ob_start();
        extract($vars, EXTR_SKIP);
        require_once($viewPath);
        return ob_get_clean();
    }

}