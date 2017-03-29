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
            case 'help':
                echo "migrate [<...entityName>] [--force|help]\nIf no entityName(s) are provided, it will map the databases entity to create it.\n\noptions:\n    --force : will overwrite existing files.\n    --help : will display the help.\n";
                exit(0);
                break;
            default:
                break;
        }
    }
    else
        $tables[] = strtolower($arg);
}

ORM::generateEntity($tables, $force);