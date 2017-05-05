<?php

namespace Core\Command;

use Core\Autoloader;

class Boot{

    public static function boot(){
        define('DS', DIRECTORY_SEPARATOR);
        define('ROOT', dirname(dirname(__DIR__)) . DS);
        define('CORE', ROOT . 'core' . DS);

        require_once CORE . 'shared' . DS . 'constants.php';
        require_once CORE . 'Autoloader.php';

        Autoloader::register();
    }

}