<?php

use Core\Http\Router;

Router::on('/', 'index@index');

Router::on('/user/', 'user@index');

Router::on('/user/:id', 'user@profile');