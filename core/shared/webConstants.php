<?php

use Core\Config;

define('WEBHOST', $_SERVER['HTTP_HOST']);
define('WEBROOT', 'http://' . WEBHOST . container(Config::class)['url']['base']);
define('WEBAPP', WEBROOT . 'app/');
define('WEBASSET', WEBAPP . 'assets/');