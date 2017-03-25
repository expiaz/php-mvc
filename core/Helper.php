<?php
namespace Core;

abstract class Helper{

    public static function getClassNameFromInstance($instance){
        $instanceNs = get_class($instance);
        $instanceClass = substr($instanceNs, strrpos($instanceNs, '\\') + 1);
        $name = ucfirst(strtolower(str_replace('Entity','',str_replace('Model','',str_replace('Controller','',$instanceClass)))));
        return $name;
    }

    public static function normalizeName($name){
        return ucfirst(strtolower($name));
    }

    public static function getNamespaceFromInstance($instance){
        $instanceNs = get_class($instance);
        $ns = substr($instanceNs, 0, strrpos($instanceNs, '\\'));
        return $ns;
    }

    public static function getModelNamespaceFromInstance($instance){
        $name = self::getClassNameFromInstance($instance);
        $model = "App\\Model\\{$name}Model";
        return $model;
    }

    public static function getModelFilePathFromInstance($instance){
        $name = self::getClassNameFromInstance($instance);
        $model = MODEL . "{$name}Model.php";
        return $model;
    }

    public static function getControllerNamespaceFromInstance($instance){
        $name = self::getClassNameFromInstance($instance);
        $model = "App\\Controller\\{$name}Controller";
        return $model;
    }

    public static function getControllerFilePathFromInstance($instance){
        $name = self::getClassNameFromInstance($instance);
        $model = CONTROLLER . "{$name}Controller.php";
        return $model;
    }

    public static function getEntityNamespaceFromInstance($instance){
        $name = self::getClassNameFromInstance($instance);
        $model = "App\\Entity\\{$name}Entity";
        return $model;
    }

    public static function getEntityFilePathFromInstance($instance){
        $name = self::getClassNameFromInstance($instance);
        $model = ENTITY . "{$name}Entity.php";
        return $model;
    }

    public static function getModelNamespaceFromName($name){
        $name = self::normalizeName($name);
        $model = "App\\Model\\{$name}Model";
        return $model;
    }

    public static function getModelFilePathFromName($name){
        $name = self::normalizeName($name);
        $model = MODEL . "{$name}Model.php";
        return $model;
    }

    public static function getControllerNamespaceFromName($name){
        $name = self::normalizeName($name);
        $model = "App\\Controller\\{$name}Controller";
        return $model;
    }

    public static function getControllerFilePathFromName($name){
        $name = self::normalizeName($name);
        $model = CONTROLLER . "{$name}Controller.php";
        return $model;
    }

    public static function getEntityNamespaceFromName($name){
        $name = self::normalizeName($name);
        $model = "App\\Entity\\{$name}Entity";
        return $model;
    }

    public static function getEntityFilePathFromName($name){
        $name = self::normalizeName($name);
        $model = ENTITY . "{$name}Entity.php";
        return $model;
    }

}