<?php

namespace Core\Database\Orm\Schema;

class Field implements Statementizable, Schematizable {

    private $table;
    private $name;
    private $type;
    private $length;
    private $default;
    private $nullable;
    private $autoIncrement;
    private $formType;

    private $selected; //is selected for the default form creation

    private $constraints;
    private $inlineConstraints;

    public function __construct(Table $table, $name)
    {
        $this->table = $table;
        $this->name = $name;
        $this->autoIncrement = false;
        $this->nullable = false;
        $this->constraints = [];
        $this->inlineConstraints = [];
    }

    public function getName(){
        return $this->name;
    }

    public function type($type){
        switch(strtolower($type)){
            case 'file':
            case 'image':
                $formType = 'FILE';
            case 'path':
            case 'url':
                $type = 'TEXT';
                $this->length(500);
                break;
            case 'boolean':
                $type = 'TINYINT';
                $formType = 'BOOLEAN';
                $this->length(1);
                break;
        }
        $this->formType = $formType ?? $type;
        $this->type = strtoupper($type);
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
        if(in_array(Constraint::PRIMARY_KEY, $this->inlineConstraints)) return $this;
        $this->table->primaryKey($this);
        $this->inlineConstraints[] = Constraint::PRIMARY_KEY;
        $this->nullable = false;
        return $this;
    }

    public function index(){
        if(in_array(Constraint::INDEX, $this->inlineConstraints)) return $this;
        $this->table->indexKey($this);
        $this->inlineConstraints[] = Constraint::INDEX;
        $this->nullable = false;
        return $this;
    }

    public function unique(){
        if(in_array(Constraint::UNIQUE, $this->inlineConstraints)) return $this;
        $this->table->uniqueKey($this);
        $this->inlineConstraints[] = Constraint::UNIQUE;
        $this->nullable = false;
        return $this;
    }

    public function check($check){
        $constraint = new Constraint($this->table, $this, "CHECK_{$this->table->getName()}({$this->name})");
        $constraint->check($check);
        $this->table->addConstraint($constraint);
        $this->constraints[] = $constraint;
        return $this;
    }

    public function defaultFormSelection(){
        $this->selected = true;
        $this->table->defaultSelection($this);
        return $this;
    }



    public function manyToMany($table, $field, $type, $length = null){
        $constraint = new Constraint($this->table, $this, "FK_{$this->table->getName()}({$this->name})_{$table}({$field})_MANYTOMANY");
        $constraint->manyToMany($table, $field, $type, $length);
        $this->table->addConstraint($constraint);
        $this->constraints[] = $constraint;
        return $this;
    }

    public function manyToOne($table, $field){
        $constraint = new Constraint($this->table, $this, "FK_{$this->table->getName()}({$this->name})_{$table}({$field})_MANYTOONE");
        $constraint->manyToOne($table, $field);
        $this->table->addConstraint($constraint);
        $this->constraints[] = $constraint;
        return $this;
    }

    public function oneToOne($table, $field){
        $constraint = new Constraint($this->table, $this, "FK_{$this->table->getName()}({$this->name})_{$table}({$field})_ONETOONE");
        $constraint->oneToOne($table, $field);
        $this->table->addConstraint($constraint);
        $this->table->uniqueKey($this->name);
        $this->constraints[] = $constraint;
        return $this;
    }



    public function statement()
    {
        $props = [];
        $props[] = $this->name;
        $props[] = $this->length ? "{$this->type}({$this->length})" : $this->type;
        $props[] = $this->default ? $this->nullable ? "DEFAULT NULL" : "DEFAULT {$this->default}" : $this->nullable === true ? "NOT NULL" : NULL;
        $props[] = $this->autoIncrement ? "AUTO_INCREMENT" : NULL;

        $fieldDecalaration = implode(' ', array_filter($props, function($e){
            return $e !== NULL;
        }));

        return $fieldDecalaration;
    }

    public function &schema() :array
    {

        $schema = [];
        $schema['name'] = $this->name;
        $schema['type'] = $this->type;
        $schema['formtype'] = $this->formType;
        $schema['length'] = $this->length;
        $schema['null'] = $this->nullable;
        $schema['default'] = $this->default !== null ? $this->default : ($this->nullable ? 'NULL' : 'NOT NULL');
        $schema['auto'] = $this->autoIncrement;
        $schema['constraints'] = [];
        foreach ($this->constraints as $c)
            $schema['constraints'][] = $c->schema();
        foreach ($this->inlineConstraints as $ic)
            $schema['constraints'][] = ['type' => $ic];

        return $schema;
    }

}