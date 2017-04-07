<?php

namespace Core\Database\Orm\Schema;

class Constraint implements Describable {

    private $table;
    private $field;
    private $referenceTable;
    private $referenceField;
    private $type;
    private $constraint;

    const PRIMARY_KEY = 1;
    const ONE_TO_ONE = 2;
    const ONE_TO_MANY = 3;
    const MANY_TO_MANY = 4;
    const CHECK = 5;
    const INDEX = 6;
    const UNIQUE = 7;
    const FULL_TEXT = 8;
    const SPATIAL = 9;
	const AI = 10;

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

    public function oneToMany($table, $field){
        $this->referenceTable = $table;
        $this->referenceField = $field;
        $this->type = self::ONE_TO_MANY;
        return $this;
    }

    public function manyToMany($table, $field){
        $this->referenceTable = $table;
        $this->referenceField = $field;
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

    public function statial(){
        $this->type = self::SPATIAL;
        return $this;
    }

    public function fulltext(){
        $this->type = self::FULL_TEXT;
        return $this;
    }

    public function check($check){
        $this->type = self::CHECK;
        $this->constraint = $check;
        return $this;
    }
	
	public function autoIncrement(){
		$this->type = self::AI;
	}

    public function describe()
    {
        switch($this->type){
            case self::PRIMARY_KEY:
                return $this->describePrimaryKey();
            case self::MANY_TO_MANY:
                return $this->describeManyToMany();
            case self::ONE_TO_MANY:
                return $this->describeOneToMany();
            case self::ONE_TO_ONE:
                return $this->describeOneToOne();
            case self::CHECK:
                return $this->describeCheck();
            case self::INDEX:
                return $this->describeIndex();
            case self::UNIQUE:
                return $this->describeUnique();
            case self::SPATIAL:
                return $this->describeSpatial();
            case self::FULL_TEXT:
                return $this->describeFullText();
			case self::AI:
				return $this->describeAI();
        }
    }

    private function describeCheck(){
        $name = $this->name ?? "CHECK_{$this->table}($this->field)";
        return ["ALTER TABLE {$this->table} ADD CONSTRAINT {$name} CHECK ({$this->check})"];
    }



    private function describePrimaryKey(){
        $name = $this->name ?? "PK_{$this->table}($this->field)";
        return [
            "ALTER TABLE {$this->table} ADD CONSTRAINT {$name} PRIMARY KEY (`{$this->field}`);",
        ];
    }

    private function describeIndex(){
        $name = $this->name ?? "PK_{$this->table}($this->field)";
        return ["CREATE INDEX {$name} ON `{$this->table}` (`{$this->field}`);"];
    }

    private function describeUnique(){
        $name = $this->name ?? "PK_{$this->table}($this->field)";
        return ["CREATE UNIQUE INDEX {$name} ON `{$this->table}` (`{$this->field}`);"];
    }

    private function describeSpatial(){
        $name = $this->name ?? "PK_{$this->table}($this->field)";
        return ["CREATE SPATIAL INDEX {$name} ON `{$this->table}` (`{$this->field}`);"];
    }

    private function describeFullText(){
        $name = $this->name ?? "PK_{$this->table}($this->field)";
        return ["CREATE FULLTEXT INDEX {$name} ON `{$this->table}` (`{$this->field}`);"];
    }

    private function describeManyToMany(){
        $table = new Table("{$this->table}_{$this->referenceTable}");
        $table->addField("{$this->table}_id")->primaryKey();
        $table->addField("{$this->referenceTable}_id")->primaryKey();
        $firstConstraint = $this->name ?? "FK_{$this->table}_{$this->referenceTable}({$this->table}_id)_{$this->table}({$this->field});";
        $secondConstraint = $this->name ?? "FK_{$this->table}_{$this->referenceTable}({$this->referenceTable}_id)_{$this->referenceTable}({$this->referenceField});";
        return [
            implode('',$table->describe()),
			"ALTER TABLE {$this->table}_{$this->referenceTable} ADD CONSTRAINT {$firstConstraint} FOREIGN KEY ({$this->field}_id) REFERENCES {$this->table}({$this->field}), ADD CONSTRAINT {$secondConstraint} FOREIGN KEY ({$this->referenceField}_id) REFERENCES {$this->referenceTable}($this->referenceField);"
        ];
    }

    private function describeOneToMany(){
        $name = $this->name ?? "FK_{$this->table}($this->field)_{$this->referenceTable}({$this->referenceField})";
        return ["ALTER TABLE {$this->table} ADD CONSTRAINT {$name} FOREIGN KEY ({$this->field}) REFERENCES {$this->referenceTable}($this->referenceField);"];
    }

    private function describeOneToOne()
    {
        $name = $this->name ?? "FK_{$this->table}($this->field)_{$this->referenceTable}({$this->referenceField})";
        return ["ALTER TABLE {$this->table} ADD CONSTRAINT {$name} FOREIGN KEY ({$this->field}) REFERENCES {$this->referenceTable}($this->referenceField);"];
    }
	
	private function describeAI(){
		return ["ALTER TABLE `{$this->table}` MODIFY `{$this->field}` int(11) NOT NULL AUTO_INCREMENT;"];
	}

}
