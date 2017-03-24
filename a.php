<?php

$arr = [
    'a/b' => [1,2,3],
    'a/b/c' => [1,3,2]
];

$array = [
    [
        'route' => 'a/:b',
        'regex' => 'a/(\w+)',
        'handler' => [
            'controller' => 'index',
            'action' => 'index'
        ]
    ],
    [
        'route' => 'a/:b/:c',
        'regex' => 'a/(\w+)/(\w+)',
        'handler' => [
            'controller' => 'index',
            'action' => 'showall'
        ]
    ]
];

usort($array, function($a,$b){
    return substr_count($b['route'],'/') <=> substr_count($a['route'],'/');
});

$handler = 'a@b';
$i = strpos($handler, '@');
$a = [
    'controller' => substr($handler,0,$i),
    'action' => substr($handler,$i+1)
];
print_r($a);