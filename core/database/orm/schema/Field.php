<?php

namespace Core\Database\Orm\Schema;

use Core\Form\Field\AbstractInputField;

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

    public function type($type): Field{
        switch(strtolower($type)){
            case 'file':
            case 'image':
                $formType = AbstractInputField::FILE;
            case 'path':
            case 'url':
                $type = 'TEXT';
                $this->length(500);
                break;
            case 'boolean':
                $type = 'TINYINT';
                $formType = AbstractInputField::BOOLEAN;
                $this->length(1);
                break;
            case 'password':
                $type = 'varchar';
                $formType = AbstractInputField::PASSWORD;
                $this->length(255);
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

    public function primaryKey(): Field{
        if(in_array(Constraint::PRIMARY_KEY, $this->inlineConstraints)) return $this;
        $this->table->primaryKey($this);
        $this->inlineConstraints[] = Constraint::PRIMARY_KEY;
        $this->nullable = false;
        return $this;
    }

    public function index(): Field{
        if(in_array(Constraint::INDEX, $this->inlineConstraints)) return $this;
        $this->table->indexKey($this);
        $this->inlineConstraints[] = Constraint::INDEX;
        $this->nullable = false;
        return $this;
    }

    public function unique(): Field{
        if(in_array(Constraint::UNIQUE, $this->inlineConstraints)) return $this;
        $this->table->uniqueKey($this);
        $this->inlineConstraints[] = Constraint::UNIQUE;
        $this->nullable = false;
        return $this;
    }

    public function check($check): Field{
        $constraint = new Constraint($this->table, $this);
        $constraint->check($check);
        $this->table->addConstraint($constraint);
        $this->constraints[] = $constraint;
        return $this;
    }

    public function defaultFormSelection(): Field{
        $this->selected = true;
        $this->table->defaultSelection($this);
        return $this;
    }



    public function manyToMany($table = null, $field = null, $type = null): Constraint{
        $constraint = new Constraint($this->table, $this);
        $constraint->manyToMany();

        if(! is_null($table))
            $constraint->reference($table);
        if(! is_null($field))
            $constraint->on($field);
        if(! is_null($type))
            $constraint->type($type);

        $this->table->addConstraint($constraint);
        $this->constraints[] = $constraint;
        return $constraint;
    }

    public function manyToOne($table = null, $field = null): Constraint{
        $constraint = new Constraint($this->table, $this);
        $constraint->manyToOne();

        if(! is_null($table))
            $constraint->reference($table);
        if(! is_null($field))
            $constraint->on($field);

        $this->table->addConstraint($constraint);
        $this->constraints[] = $constraint;
        
        return $constraint;
    }

    public function oneToOne($table = null, $field = null): Constraint{
        $constraint = new Constraint($this->table, $this);
        $constraint->oneToOne();

        if(! is_null($table))
            $constraint->reference($table);
        if(! is_null($field))
            $constraint->on($field);

        $this->table->addConstraint($constraint);
        $this->table->uniqueKey($this);
        $this->constraints[] = $constraint;
        return $constraint;
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