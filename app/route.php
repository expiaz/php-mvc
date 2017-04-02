<?php

use Core\Http\Router;

Router::on('/', 'film@index');
Router::on('/add', 'film@add');

Router::on('/realisateur', 'realisateur@index');
Router::on('/realisateur/profile/:id', 'realisateur@profile');
