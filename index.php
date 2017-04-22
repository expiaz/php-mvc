<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', __DIR__ . DS);
define('CORE', ROOT . 'core' . DS);

require_once CORE . 'shared' . DS . 'constants.php';
require_once CORE . 'shared' . DS . 'helpers.php';
require_once CORE . 'App.php';

$app = Core\App::init($_GET['p'] ?? '');