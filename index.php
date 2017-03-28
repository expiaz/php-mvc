<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__) . DS);
define('CORE', ROOT . 'core' . DS);
require_once CORE . 'constants.php';

require_once CORE . 'Bootstrapper.php';
require_once CORE . 'Dispatcher.php';

new Core\Bootstrapper();

\Core\Database\ORM::generateEntity();
//new Core\Dispatcher($_GET['p'] ?? '');