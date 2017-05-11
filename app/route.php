<?php


use Core\Facade\Contracts\RouterFacade;

RouterFacade::on('*', 'index@default');

RouterFacade::get('/a', 'index@index');
RouterFacade::get('*', 'index@allget');
RouterFacade::post('*', 'index@allpost');