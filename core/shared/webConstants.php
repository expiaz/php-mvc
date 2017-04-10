<?php

define('WEBHOST', $_SERVER['HTTP_HOST']);
define('WEBROOT', 'http://' . WEBHOST . \Core\Config::getBaseURI());
define('WEBAPP', WEBROOT . 'app/');
define('WEBASSET', WEBAPP . 'assets/');