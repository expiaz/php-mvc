<?php

define('DEV', false);

if(DEV){
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

define('APP', ROOT . 'app' . DS);
define('CONTROLLER', APP . 'controller' . DS);
define('MODEL', APP . 'model' . DS);
define('ENTITY', APP . 'entity' . DS);
define('VIEW', APP . 'view' . DS);
define('LAYOUT', VIEW . 'layout' . DS);



define('CORE_MVC', CORE . 'mvc' . DS);
define('CORE_HTTP', CORE . 'http' . DS);
define('CORE_DATABASE', CORE . 'database' . DS);
define('CORE_FORM', CORE . 'form' . DS);

define('WEBHOST', $_SERVER['HTTP_HOST']);
define('WEBROOT', 'http://' . WEBHOST . '/appsynth/');
define('WEBAPP', WEBROOT . 'app/');
define('WEBASSET', WEBAPP . 'assets/');