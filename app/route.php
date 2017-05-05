<?php

use Core\Http\Router;

$router = container(Router::class);

//$router->default('index@default');

$router->on('/', 'index@index');
$router->on('/:id', 'index@id');



