<?php

//TODO too much
// - response + request http v
// - orm constraints + form types
// - view v
// - router middleware and slug
// - container resolve __invoke
// - router resolve with Ns or Names for controllers + Invoke
// - Auth + Guard

use Core\Autoloader;

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', __DIR__ . DS);
define('CORE', ROOT . 'core' . DS);

require_once CORE . 'shared' . DS . 'constants.php';
require_once CORE . 'shared' . DS . 'helpers.php';
require_once CORE . 'Autoloader.php';

Autoloader::register();

$app = Core\App::init($_SERVER['REDIRECT_URL'] ?? '');