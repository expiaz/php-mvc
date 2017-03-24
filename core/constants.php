<?php

define('DEV', false);

define('APP', ROOT . 'app' . DS);
define('CONTROLLER', APP . 'controller' . DS);
define('MODEL', APP . 'model' . DS);
define('ENTITY', APP . 'entity' . DS);
define('VIEW', APP . 'view' . DS);


define('CORE_MVC', CORE . 'mvc' . DS);
define('CORE_HTTP', CORE . 'http' . DS);
define('CORE_DATABASE', CORE . 'database' . DS);

define('WEBHOST', $_SERVER['HTTP_HOST']);
define('WEBROOT', 'http://' . WEBHOST . '/appsynth/');
define('WEBAPP', 'http://' . WEBHOST . '/appsynth/app/');
define('WEBASSET', 'http://' . WEBHOST . '/appsynth/app/assets/');