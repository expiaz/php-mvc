<?php



echo $a = password_hash('azerty',PASSWORD_BCRYPT, [
    'salt' => 'thisisachainof22characters'
    ] ) . "\n";

$login = 'abc';
$pwd = 'def';
$a = compact($login, $pwd);

var_dump($a);