<?php

use Core\Autoloader;

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', __DIR__ . DS);
define('CORE', ROOT . 'core' . DS);

require_once CORE . 'shared' . DS . 'constants.php';
require_once CORE . 'shared' . DS . 'helpers.php';
require_once CORE . 'Autoloader.php';

Autoloader::register();

require_once 'core/Config.php';

$a = new \Core\Config();

var_dump($a->url);