<?php

define('DEV', false);

if(DEV){
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

define('APP', ROOT . 'app' . DS);
define('CONTROLLER', APP . 'controller' . DS);
define('MODEL', APP . 'model' . DS);
define('ENTITY', MODEL);
define('REPOSITORY', APP . 'repository' . DS);
define('SCHEMA', MODEL . 'schema' . DS);
define('VIEW', APP . 'view' . DS);
define('LAYOUT', VIEW . 'layout' . DS);

define('CORE_MVC', CORE . 'mvc' . DS);
define('CORE_SHARED', CORE . 'shared' . DS);
define('CORE_HTTP', CORE . 'http' . DS);
define('CORE_DATABASE', CORE . 'database' . DS);
define('CORE_FORM', CORE . 'form' . DS);
define('CORE_ORM', CORE_DATABASE . 'orm' . DS);