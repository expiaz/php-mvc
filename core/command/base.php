<?php

namespace Core\Command;

use Core\Autoload;

class Boot{

    public static function boot(){
        define('DS', DIRECTORY_SEPARATOR);
        define('ROOT', dirname(dirname(dirname(__FILE__))) . DS);
        define('CORE', ROOT . 'core' . DS);
        define('ENTITY', ROOT . 'app' . DS . 'entity' . DS);
        define('CONTROLLER', ROOT . 'app' . DS . 'controller' . DS);
        define('MODEL', ROOT . 'app' . DS . 'model' . DS);
        define('DEV', false);

        require_once CORE . 'Autoload.php';

        Autoload::register();
    }

}