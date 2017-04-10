<?php

namespace Core\Database\Orm\Schema;

class InlineConstraint implements Describable {

    const PRIMARY_KEY = 1;
    const INDEX = 3;
    const UNIQUE = 4;

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

    public function describe()
    {
        $fields = '`' . implode('`, `', $this->fields) . '`';
        $typeName = implode('_', $this->fields);;
        switch ($this->type){
            case self::PRIMARY_KEY:
                return "PRIMARY KEY ({$fields})";
                break;
            case self::INDEX:
                return "KEY `INDEX_{$typeName}` ({$fields})";
                break;
            case self::UNIQUE:
                return "UNIQUE KEY `UNIQUE_{$typeName}` ({$fields})";
                break;
        }
    }

}