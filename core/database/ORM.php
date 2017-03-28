<?php

namespace Core\Database;

use PDO;

final class ORM{

    private static $_pdo = null;

    private static function getFieldChilds($fieldName){
        $pdo = self::getPDO();
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
                $describe['childs'] = self::getFieldChilds($field['Field']);
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

    public static function describeTable($tableName){
        $table = [];

        $pdo = self::getPDO();
        $query = $pdo->query("DESCRIBE {$tableName}");
        $fields = $query->fetchAll();
        foreach ($fields as $field){
            $table[] = self::describeField($field);
        }

        return $table;
    }

    public static function describe(){
        $bdd = [];

        $pdo = self::getPDO();
        $query = $pdo->query("SHOW TABLES");
        $tables = $query->fetchAll();
        foreach ($tables as $table){
            $bdd[$table[0]] = self::describeTable($table[0]);
        }

        return $bdd;
    }

    private static function getPDO(){
        if(self::$_pdo)
            return self::$_pdo;
        return self::$_pdo = Database::getInstance();
    }

}