<?php

namespace Core\Database\Orm\Schema;

class Field implements Describable {

    private $table;
    private $name;
    private $type;
    private $length;
    private $default;
    private $nullable;
    private $autoIncrement;

    private $constraints;
    private $pkConstraint = null;
    private $keyConstraint = null;
    private $uniqueConstraint = null;

    public function __construct(Table $table, $name)
    {
        $this->table = $table;
        $this->name = $name;
        $this->autoIncrement = false;
        $this->nullable = false;
        $this->constraints = [];
    }

    public function getName(){
        return $this->name;
    }

    public function type($type){
        $this->type = $type;
        return $this;
    }

    public function getType(){
        return $this->type;
    }

    public function length($length){
        $this->length = $length;
        return $this;
    }

    public function getLength(){
        return $this->length;
    }

    public function default($value){
        $this->default = $value;
        return $this;
    }

    public function getDefault(){
        return $this->default;
    }

    public function nullable(){
        $this->nullable = true;
        return $this;
    }

    public function getNullable(){
        return $this->nullable;
    }

    public function autoIncrement(){
        $this->autoIncrement = true;
        $this->type('INT');
        $this->length(11);
        $this->primaryKey();
        return $this;
    }

    public function getAutoIncrement(){
        return $this->autoIncrement;
    }

    public function primaryKey(){
        $this->table->primaryKey($this->name);
        $this->nullable = false;
        return $this;
    }

    public function index(){
        $this->table->indexKey($this->name);
        return $this;
    }

    public function unique(){
        $this->table->uniqueKey($this->name);
        return $this;
    }

    public function check($check){
        $constraint = new Constraint($this->table, $this, "CHECK_{$this->table->getName()}({$this->name})");
        $constraint->check($check);
        $this->table->addConstraint($constraint);
        return $this;
    }



    public function manyToMany($table, $field){
        $constraint = new Constraint($this->table, $this, "FK_{$this->table->getName()}({$this->name})_{$table}({$field})_MANYTOMANY");
        $constraint->manyToMany($table, $field);
        $this->table->addConstraint($constraint);
        return $this;
    }

    public function manyToOne($table, $field){
        $constraint = new Constraint($this->table, $this, "FK_{$this->table->getName()}({$this->name})_{$table}({$field})_MANYTOONE");
        $constraint->manyToOne($table, $field);
        $this->table->addConstraint($constraint);
        return $this;
    }

    public function oneToOne($table, $field){
        $constraint = new Constraint($this->table, $this, "FK_{$this->table->getName()}({$this->name})_{$table}({$field})_ONETOONE");
        $constraint->oneToOne($table, $field);
        $this->table->addConstraint($constraint);
        $this->table->uniqueKey($this->name);
        return $this;
    }



    public function describe()
    {
        $props = [];
        $props[] = $this->name;
        $props[] = $this->length ? "{$this->type}({$this->length})" : $this->type;
        $props[] = $this->default ? $this->nullable ? "DEFAULT NULL" : "DEFAULT {$this->default}" : $this->nullable == false ? "NOT NULL" : NULL;
        $props[] = $this->autoIncrement ? "AUTO_INCREMENT" : NULL;

        $fieldDecalaration = implode(' ', array_filter($props, function($e){
            return $e !== NULL;
        }));

        return $fieldDecalaration;
    }

}