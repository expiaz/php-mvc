<?php

namespace Core\Database\Orm\Schema;

use Closure;
use Core\Exception\FileNotFoundException;
use Core\Helper;

abstract class Schema{

    public abstract function schema();

    public static function get($entityNs): Table{
        if(is_object($entityNs))
            $entityNs = get_class($entityNs);
        if(is_array($entityNs))
            $entityNs = get_class((object) $entityNs);

        if(Helper::isValidNamespace($entityNs)){
            $ns = Helper::getSchemaNamespaceFromName(Helper::getClassNameFromNamespace($entityNs));;
            return new $ns();
        }
        throw new \Exception("Schema::get {$entityNs} schema not found");
    }

}