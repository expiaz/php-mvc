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

    public function __construct($table, $name)
    {
        $this->table = $table;
        $this->name = $name;
        $this->autoIncrement = false;
        $this->nullable = false;
        $this->constraints = [];
    }

    public function type($type){
        $this->type = $type;
        return $this;
    }

    public function length($length){
        $this->length = $length;
        return $this;
    }

    public function default($value){
        $this->default = $value;
        return $this;
    }

    public function nullable(){
        $this->nullable = true;
        return $this;
    }

    public function primaryKey(){
        $constraint = new Constraint($this->table, $this->name, "PK_{$this->table}");
        $constraint->primaryKey();
        $this->constraints[] = $constraint;
        return $this;
    }

    public function index(){
        $constraint = new Constraint($this->table, $this->name, "INDEX_{$this->table}({$this->name})");
        $constraint->index();
        $this->constraints[] = $constraint;
        return $this;
    }

    public function unique(){
        $constraint = new Constraint($this->table, $this->name, "UNIQUE_{$this->table}({$this->name})");
        $constraint->unique();
        $this->constraints[] = $constraint;
        return $this;
    }

    public function manyToMany($table, $field){
        $constraint = new Constraint($this->table, $this->name, "FK_{$this->table}({$this->name})_{$table}({$field})_MANYTOMANY");
        $constraint->manyToMany($table, $field);
        $this->constraints[] = $constraint;
        return $this;
    }

    public function manyToOne($table, $field){
        $constraint = new Constraint($this->table, $this->name, "FK_{$this->table}({$this->name})_{$table}({$field})_MANYTOONE");
        $constraint->manyToMany($table, $field);
        $this->constraints[] = $constraint;
        return $this;
    }

    public function oneToOne($table, $field){
        $constraint = new Constraint($this->table, $this->name, "FK_{$this->table}({$this->name})_{$table}({$field})_ONETOONE");
        $constraint->oneToOne($table, $field);
        $this->constraints[] = $constraint;
        return $this;
    }

    public function check($check){
        $constraint = new Constraint($this->table, $this->name, "CHECK_{$this->table}({$this->name})");
        $constraint->check($check);
        $this->constraints[] = $constraint;
        return $this;
    }

    public function autoIncrement(){
        $this->autoIncrement = true;
		$constraint = new Constraint($this->table, $this->name);
        $constraint->autoIncrement();
        $this->constraints[] = $constraint;
        return $this;
    }

    /**
     * @Deprecated
     */
    public function addConstraint($name = null){
        $constraint = new Constraint($this->table, $this->name, $name);
        $this->constraints[] = $constraint;
        return $constraint;
    }

    public function describe()
    {
        $props = [];
        $props[] = $this->name;
        $props[] = $this->length ? "{$this->type}({$this->length})" : $this->type;
        $props[] = $this->default ? $this->nullable ? "DEFAULT NULL" : "DEFAULT {$this->default}" : NULL;

        return implode(' ', array_filter($props, function($e){
            return $e !== NULL;
        }));
    }

    public function getConstraints(){
        return $this->constraints;
    }

}