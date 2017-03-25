<?php

use Core\Http\Router;

Router::on('/', 'film@index');

    Router::on('/film/', 'film@index');

Router::on('/film/profile/:id', 'film@profile');

Router::on('/realisateur/', 'realisateur@index');

Router::on('/realisateur/profile/:id', 'realisateur@profile');