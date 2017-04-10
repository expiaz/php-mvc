<?php

namespace Core\Database\Orm\Schema;

class Table implements Describable {

    private $fields;
    private $prefix;
    private $name;

    private $constraints;
    private $pkConstraint;
    private $keyConstraint;
    private $uniqueConstraint;

    public function __construct($name)
    {
        $this->name = $name;
        $this->fields = [];
        $this->constraints = [];
    }

    public function getName(){
        return $this->name;
    }

    public function prefix($prefix){
        $this->prefix = $prefix;
    }

    public function getPrefix(){
        return $this->prefix;
    }

    public function addField($name){
        $field = new Field($this, $this->prefix ? $this->prefix.'_'.$name : $name);
        $this->fields[] = $field;
        return $field;
    }

    public function primaryKey($name){
        if(is_null($this->pkConstraint)){
            $this->pkConstraint = new InlineConstraint(InlineConstraint::PRIMARY_KEY, $name);
        }
        else{
            $this->pkConstraint->addField($name);
        }
    }

    public function indexKey($name){
        if(is_null($this->keyConstraint)){
            $this->keyConstraint = new InlineConstraint(InlineConstraint::INDEX, $name);
        }
        else{
            $this->keyConstraint->addField($name);
        }
    }

    public function uniqueKey($name){
        if(is_null($this->uniqueConstraint)){
            $this->uniqueConstraint = new InlineConstraint(InlineConstraint::UNIQUE, $name);
        }
        else{
            $this->uniqueConstraint->addField($name);
        }
    }

    public function addConstraint(Constraint $constraint){
        $this->constraints[] = $constraint;
    }

    public function describe(): array
    {
        $transaction = [];
        $fieldsDescribed = array_map(function(Field $e){
            return $e->describe();
        }, $this->fields);

        if($this->pkConstraint instanceof InlineConstraint) $fieldsDescribed[] = $this->pkConstraint->describe();
        if($this->keyConstraint instanceof InlineConstraint) $fieldsDescribed[] = $this->keyConstraint->describe();
        if($this->uniqueConstraint instanceof InlineConstraint) $fieldsDescribed[] = $this->uniqueConstraint->describe();

        $fieldsSql = "\t" . implode(",\n\t",$fieldsDescribed);
        $tableSql = "CREATE TABLE {$this->name} (\n {$fieldsSql} \n) ENGINE=InnoDB;";
        $transaction[] = $tableSql;

        foreach ($this->constraints as $constraint)
            $transaction = array_merge($transaction, $constraint->describe());

        return $transaction;
    }

}