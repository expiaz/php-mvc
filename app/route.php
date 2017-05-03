<?php

use Core\Http\Router;

$router = container(Router::class);

$router->on('/a/b/c', 'index@a');


