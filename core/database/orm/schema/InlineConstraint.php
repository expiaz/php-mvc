<?php

namespace Core\Database\Orm\Schema;

class InlineConstraint implements Statementizable, Schematizable {

    const PRIMARY_KEY = 1;
    const INDEX = 6;
    const UNIQUE = 7;

    private $type;
    private $name;
    private $fields;

    public function __construct($type, ...$fields)
    {
        $this->type = $type;
        $this->name = null;
        $this->fields = $fields;
    }

    public function addField($field){
        $this->fields[] = $field;
    }

    public function getFields(){
        return $this->fields;
    }

    public function statement()
    {
        $fields = '`' . implode('`, `', $this->fields) . '`';
        $typeName = implode('_', $this->fields);;
        switch ($this->type){
            case static::PRIMARY_KEY:
                return "PRIMARY KEY ({$fields})";
                break;
            case static::INDEX:
                return "KEY `INDEX_{$typeName}` ({$fields})";
                break;
            case static::UNIQUE:
                return "UNIQUE KEY `UNIQUE_{$typeName}` ({$fields})";
                break;
        }
    }

    public function schema():array
    {
        return [
            "type" => $this->type,
            "fields" => $this->fields
        ];
    }

}