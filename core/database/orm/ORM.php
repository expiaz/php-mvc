<?php

namespace Core\Database\Orm;

use PDO;

final class ORM{

    private static $_pdo = null;

    private static function getFieldChilds($fieldName){
        $pdo = static::getPDO();
        $sql = "SELECT * FROM {$fieldName}";
        $query = $pdo->query($sql);
        $childs = $query->fetchAll();
        $childsField = [];

        foreach ($childs as $child) {
            $childsField[] = [
                'name' => $child[1],
                'value' => $child[0]
            ];
        }

        return $childsField;
    }

    public static function describeField($fieldDescription){
        $describe = [];
        $field = $fieldDescription;

        if(preg_match('#char|text#i',$field['Type'])){
            $describe['type'] = 'text';
            preg_match('#\((\d*)\)#',$field['Type'],$max);
            $describe['maxlength'] = $max[1] ?? false;
        }
        elseif(preg_match('#date|time#i',$field['Type'])){
            $describe['type'] = 'date';
        }
        else{
            $describe['type'] = 'number';
            preg_match('#\((\d*)\)#',$field['Type'],$max);
            $describe['maxlength'] = $max[1] ?? false;
        }

        $describe['name'] = $field['Field'];
        $describe['required'] = $field['Null'] === 'NO' ? true : false;
        $describe['value'] = $field['Default'] ?? '';

        switch ($field['Key']){
            case 'PRI':
                $describe['type'] = 'hidden';
                $describe['required'] = true;
                break;
            case 'MUL':
                $describe['type'] = 'select';
                $describe['childs'] = static::getFieldChilds($field['Field']);
                break;
            default:
                break;
        }

        switch ($field['Extra']){
            case 'auto_increment':
                $describe['type'] = 'hidden';
                break;
            default:
                break;
        }

        return $describe;
    }

    private static function describeConstraints($tableName){

    }

    public static function describeTable($tableName){
        $table = [];

        $pdo = static::getPDO();
        $query = $pdo->query("DESCRIBE {$tableName}");
        $fields = $query->fetchAll();
        foreach ($fields as $field){
            $table[] = static::describeField($field);
        }

        return $table;
    }

    public static function describe(){
        $bddSchema = [];
        $pdo = static::getPDO();

        $query = $pdo->query("SHOW TABLES");
        $tables = $query->fetchAll(PDO::FETCH_ASSOC);
        $tables = array_map(function($table){ return array_values($table)[0]; }, $tables);

        foreach ($tables as $table){
            $bddSchema[$table] = static::describeTable($table);
        }

        return $bddSchema;
    }

    private static function getPDO(){
        if(static::$_pdo)
            return static::$_pdo;
        return static::$_pdo = Database::getInstance();
    }

}