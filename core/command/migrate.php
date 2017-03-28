<?php

namespace Core\Command;

require_once 'base.php';

Boot::boot();

use Core\Command\Database\ORM;

$args = array_slice($argv,1);
$force = false;
$tables = [];
foreach ($args as $arg){
    if(substr($arg,0,2) === '--'){
        $option = substr($arg,2);
        switch(strtolower($option)){
            case 'force':
                $force = true;
                break;
            default:
                break;
        }
    }
    else
        $tables[] = strtolower($arg);
}

ORM::generateEntity($tables, $force);