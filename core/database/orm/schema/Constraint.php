<?php

namespace Core\Database\Orm\Schema;

use Core\Database\Database;

class Constraint implements Describable {

    private $table;
    private $field;
    private $referenceTable;
    private $referenceField;
    private $referenceFieldType;
    private $type;
    private $mathematicalConditionnal;

    const PRIMARY_KEY = 1;
    const ONE_TO_ONE = 2;
    const MANY_TO_ONE = 3;
    const MANY_TO_MANY = 4;
    const CHECK = 5;
    const INDEX = 6;
    const UNIQUE = 7;
	const AI = 8;

    private $name;

    public function __construct($table, $field, $name = null)
    {
        $this->name = $name;
        $this->table = $table;
        $this->field = $field;
    }

    public function primaryKey(){
        $this->referenceField = $this->field;
        $this->referenceTable = $this->table;
        $this->type = self::PRIMARY_KEY;
        return $this;
    }

    public function oneToOne($table, $field){
        $this->referenceTable = $table;
        $this->referenceField = $field;
        $this->type = self::ONE_TO_ONE;
        return $this;
    }

    public function manyToOne($table, $field){
        $this->referenceTable = $table;
        $this->referenceField = $field;
        $this->type = self::MANY_TO_ONE;
        return $this;
    }

    public function manyToMany($table, $field){
        $this->referenceTable = $table;
        $this->referenceField = $field;
        $query = Database::raw("DESCRIBE {$table} {$field};")[0];
        $this->referenceFieldType = $query->Type;
        $this->type = self::MANY_TO_MANY;
        return $this;
    }

    public function index(){
        $this->type = self::INDEX;
        return $this;
    }

    public function unique(){
        $this->type = self::UNIQUE;
        return $this;
    }

    public function check($check){
        $this->type = self::CHECK;
        $this->mathematicalConditionnal = $check;
        return $this;
    }
	
	public function autoIncrement(){
		$this->type = self::AI;
		return $this;
	}

    public function describe(): array
    {
        switch($this->type){
            case self::PRIMARY_KEY:
                return $this->describePrimaryKey();
            case self::MANY_TO_MANY:
                return $this->describeManyToMany();
            case self::MANY_TO_ONE:
                return $this->describeManyToOne();
            case self::ONE_TO_ONE:
                return $this->describeOneToOne();
            case self::CHECK:
                return $this->describeCheck();
            case self::INDEX:
                return $this->describeIndex();
            case self::UNIQUE:
                return $this->describeUnique();
			case self::AI:
				return $this->describeAI();
        }
    }



    private function describeManyToMany(){
        $tableName = "{$this->table->getName()}_{$this->referenceTable}";
        $firstField = "{$this->table->getName()}_id";
        $secondField = "{$this->referenceTable}_id";

        $table = new Table($tableName);
        $table->addField($firstField)->primaryKey()->type($this->field->getType());
        $table->addField($secondField)->primaryKey()->type($this->referenceFieldType);

        $firstConstraint = "FK_{$tableName}({$firstField})_{$this->table->getName()}({$this->field->getName()})";
        $secondConstraint = "FK_{$tableName}({$secondField})_{$this->referenceTable}({$this->referenceField})";

        return array_merge($table->describe(), [
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
        return [
            "ALTER TABLE {$this->table} ADD CONSTRAINT {$name} PRIMARY KEY (`{$this->field}`);",
        ];
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

}
