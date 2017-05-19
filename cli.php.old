<?php

if($argc < 2)
    exit("command works like: 'php cli.php <command>'\n");

switch(strtolower($argv[1])){
    case 'migrate':
        $cmd = 'php core' . DIRECTORY_SEPARATOR . 'command' . DIRECTORY_SEPARATOR . 'migrate.php ' . implode(' ', array_slice($argv,2)) . ' 2>&1';
        exec($cmd,$out);
        foreach ($out as $o) {
            echo $o . "\n";
        }
        break;
    case 'create':
        $cmd = 'php core' . DIRECTORY_SEPARATOR . 'command' . DIRECTORY_SEPARATOR . 'create.php ' . implode(' ', array_slice($argv,2)) . ' 2>&1';
        exec($cmd,$out);
        foreach ($out as $o) {
            echo $o . "\n";
        }
        break;
    default:
        echo "{$argv[1]} : command not found\n";
        break;
}