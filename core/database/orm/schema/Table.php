<?php

namespace Core\Database\Orm\Schema;

class Table implements Describable {

    private $fields;
    private $schema;
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
        $this->fields = [];
    }

    public function addField($name){
        $field = new Field($this->name, $name);
        $this->fields[] = $field;
        return $field;
    }

    public function describe()
    {
        $transaction = [];
        $fieldsDescribed = array_map(function(Field $e){
            return $e->describe();
        }, $this->fields);
        $fieldsSql = "\t" . implode(",\n\t",$fieldsDescribed);
        $tableSql = "CREATE TABLE {$this->name} (\n {$fieldsSql} \n) ENGINE=InnoDB;";
        $transaction[] = $tableSql;
        foreach ($this->fields as $field)
            foreach ($field->getConstraints() as $constraint)
                $transaction = array_merge($transaction, $constraint->describe());
        return $transaction;
    }

}