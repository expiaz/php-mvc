<?php

namespace Core\Command;

require_once 'Boot.php';

Boot::boot();

use Core\Database\Database;
use Core\Command\Files\ControllerGenerator;
use Core\Command\Files\ModelGenerator;

$args = array_slice($argv,1);

if(count($args) === 0){
    $filesType = ['controller','model'];
}
else
    switch(strtolower($args[0])){
        case 'model':
            $filesType = ['model'];
			$args = array_slice($args,1);
            break;
        case 'controller':
            $filesType = ['controller'];
			$args = array_slice($args,1);
            break;
        default:
            $filesType = ['controller','model'];
			break;
    }


$force = false;
$filesNames = [];
foreach ($args as $arg){
    if(substr($arg,0,2) === '--'){
        $option = substr($arg,2);
        switch(strtolower($option)){
            case 'force':
                $force = true;
                break;
            case 'help':
                echo "create [controller|model] [<...fileName>] [--force|help]\nIf controller / model are not set, it will create both.\nIf no fileName(s) are provided, it will map the databases entity to create it.\n\noptions:\n    --force : will overwrite existing files.\n    --help : will display the help.\n";
                exit(0);
                break;
            default:
                break;
        }
    }
    else
        $filesNames[] = strtolower($arg);
}

if(count($filesNames) === 0){
    $pdo = Database::getInstance();
    $query = $pdo->query("SHOW TABLES;");
    $tables = $query->fetchAll();
    foreach ($tables as $table){
        $filesNames[] = $table[0];
    }
}

$filesNames = array_map(function($e){
    return lcfirst(str_replace(['_','-'],'',ucwords(strtolower($e), '_-')));
}, $filesNames);

foreach ($filesType as $type){

    switch($type){
        case 'model':
            foreach ($filesNames as $name){
                new ModelGenerator($name, $force);
            }
            break;
        case 'controller':
            foreach ($filesNames as $name){
                new ControllerGenerator($name, $force);
            }
            break;
    }

}