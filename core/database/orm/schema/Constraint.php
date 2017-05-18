<?php

namespace Core\Database\Orm\Schema;

class Constraint implements Statementizable, Schematizable {

    private $table;
    private $field;
    private $referenceTable;
    private $referenceField;
    private $referenceFieldType;
    private $defaultSelection;
    private $type;
    private $mathematicalConditional;

    const PRIMARY_KEY = 1;
    const ONE_TO_ONE = 2;
    const MANY_TO_ONE = 3;
    const ONE_TO_MANY = 12;
    const MANY_TO_MANY = 4;
    const CHECK = 5;
    const INDEX = 6;
    const UNIQUE = 7;
    const AI = 8;

    private $name;

    public function __construct(Table $table, Field $field, $name = null)
    {
        $this->name = $name;
        $this->table = $table;
        $this->field = $field;
    }

    public function getType(){
        return $this->type;
    }

    public function getName(){
        return $this->name;
    }

    public function primaryKey(){
        $this->referenceField = $this->field;
        $this->referenceTable = $this->table;
        $this->type = static::PRIMARY_KEY;
        return $this;
    }

    public function oneToOne(){
        $this->type = static::ONE_TO_ONE;
        return $this;
    }

    public function manyToOne(){
        $this->type = static::MANY_TO_ONE;
        return $this;
    }

    public function manyToMany(){
        $this->type = static::MANY_TO_MANY;
        return $this;
    }

    public function reference($table){
        $this->referenceTable = $table;
        return $this;
    }

    public function on($field){
        $this->referenceField = $field;
        return $this;
    }

    public function type($type){
        $this->referenceFieldType = $type;
        return $this;
    }

    public function length($length){
        $this->referenceFieldType = "$this->referenceFieldType($length)";
        return $this;
    }

    public function defaultFormSelection($field){
        $this->defaultSelection = $field;
        return $this;
    }


    public function index(){
        $this->type = static::INDEX;
        return $this;
    }

    public function unique(){
        $this->type = static::UNIQUE;
        return $this;
    }

    public function check($check){
        $this->type = static::CHECK;
        $this->mathematicalConditional = $check;
        return $this;
    }

    public function autoIncrement(){
        $this->type = static::AI;
        return $this;
    }



    private function describeManyToMany(){
        $tableName = "{$this->table->getName()}_{$this->referenceTable}";
        $firstField = "{$this->table->getName()}_id";
        $secondField = "{$this->referenceTable}_id";

        $table = new Table($tableName);
        $table->field($firstField)->primaryKey()->type($this->field->getType());
        $table->field($secondField)->primaryKey()->type($this->referenceFieldType);

        $firstConstraint = "FK_{$tableName}({$firstField})_{$this->table->getName()}({$this->field->getName()})";
        $secondConstraint = "FK_{$tableName}({$secondField})_{$this->referenceTable}({$this->referenceField})";

        return array_merge($table->statement(), [
            "ALTER TABLE {$tableName} ADD CONSTRAINT {$firstConstraint} FOREIGN KEY ({$firstField}) REFERENCES {$this->table->getName()}({$this->field->getName()}), ADD CONSTRAINT {$secondConstraint} FOREIGN KEY ({$secondField}) REFERENCES {$this->referenceTable}($this->referenceField);"
        ]);
    }

    private function describeManyToOne(){
        $name = "FK_{$this->table->getName()}({$this->field->getName()})_{$this->referenceTable}({$this->referenceField})";
        return ["ALTER TABLE {$this->table->getName()} ADD CONSTRAINT {$name} FOREIGN KEY ({$this->field->getName()}) REFERENCES {$this->referenceTable}($this->referenceField);"];
    }

    private function describeOneToOne()
    {
        $name = "FK_{$this->table->getName()}({$this->field->getName()})_{$this->referenceTable}({$this->referenceField})";
        return ["ALTER TABLE {$this->table->getName()} ADD CONSTRAINT {$name} FOREIGN KEY ({$this->field->getName()}) REFERENCES {$this->referenceTable}($this->referenceField);"];
    }




    private function describeCheck(){
        $name = "CHECK_{$this->table}($this->field)";
        return ["ALTER TABLE {$this->table} ADD CONSTRAINT {$name} CHECK ({$this->check})"];
    }

    private function describePrimaryKey(){
        $name = "PK_{$this->table}($this->field)";
        return ["ALTER TABLE {$this->table} ADD CONSTRAINT {$name} PRIMARY KEY (`{$this->field}`);"];
    }

    private function describeIndex(){
        $name = "PK_{$this->table}($this->field)";
        return ["CREATE INDEX {$name} ON `{$this->table}` (`{$this->field}`);"];
    }

    private function describeUnique(){
        $name = "PK_{$this->table}($this->field)";
        return ["CREATE UNIQUE INDEX {$name} ON `{$this->table}` (`{$this->field}`);"];
    }

    private function describeAI(){
        return ["ALTER TABLE `{$this->table}` MODIFY `{$this->field}` INT(11) NOT NULL AUTO_INCREMENT;"];
    }


    /**
     * @Override
     * @return array
     */
    public function statement()
    {
        switch($this->type){
            case static::PRIMARY_KEY:
                return $this->describePrimaryKey();
            case static::MANY_TO_MANY:
                return $this->describeManyToMany();
            case static::MANY_TO_ONE:
                return $this->describeManyToOne();
            case static::ONE_TO_ONE:
                return $this->describeOneToOne();
            case static::CHECK:
                return $this->describeCheck();
            case static::INDEX:
                return $this->describeIndex();
            case static::UNIQUE:
                return $this->describeUnique();
            case static::AI:
                return $this->describeAI();
        }
    }


    public function schema(): array
    {
        $schema = [
            "type" => $this->type,
            "table" => $this->referenceTable,
            "field" => $this->referenceField,
            "form" => $this->defaultSelection
        ];

        return $schema;
    }

}
