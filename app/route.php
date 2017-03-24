<?php

use Core\Http\Router;

Router::on('/', 'index@index');

Router::on('/users/', 'user@index');

Router::on('/user/:id', 'user@profile');

Router::on('/user/update/:id', 'user@update');