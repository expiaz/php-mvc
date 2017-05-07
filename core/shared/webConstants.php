<?php

use Core\App;
use Core\Config;

define('WEBHOST', $_SERVER['HTTP_HOST']);
define('WEBROOT', 'http://' . WEBHOST . App::make(Config::class)->get('url')['base']);
define('WEBAPP', WEBROOT . 'app/');
define('WEBASSET', WEBAPP . 'assets/');