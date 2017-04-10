<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', __DIR__ . DS);
define('CORE', ROOT . 'core' . DS);

require_once CORE . 'shared' . DS . 'constants.php';
require_once CORE . 'Bootstrapper.php';
require_once CORE . 'Dispatcher.php';

new Core\Bootstrapper();
new Core\Dispatcher($_GET['p'] ?? '');