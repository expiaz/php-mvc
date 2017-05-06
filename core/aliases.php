<?php

$facades = [];

if($dir = opendir(CORE . 'facade' . DS . 'contracts')){
    while(($file = readdir($dir)) !== false){
        if(preg_match('/^(\w+Facade).php$/', $file, $matches)){
            $facadeNs = "Core\\Facade\\Contracts\\{$matches[1]}";
            $facades[ $facadeNs::getFacadeAccessor() ] = $facadeNs;
        }
    }
}

/*
$services = container()->getServices();

foreach ($services as $service){
    $service = trim($service, '\\');

    if(! strpos($service, '\\')){
        next($services);
    }

    $serviceName = substr($service, strrpos($service, '\\') + 1);
    $serviceNs = $service;

    $facades[ $serviceName ] = $serviceNs;
}
*/

return $facades;