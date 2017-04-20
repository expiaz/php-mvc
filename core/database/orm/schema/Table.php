<?php

namespace Core\Database\Orm\Schema;

class Table implements Statementizable, Schematizable {

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

    public function primaryKey(Field $field){
        if(is_null($this->pkConstraint)){
            $this->pkConstraint = new InlineConstraint(InlineConstraint::PRIMARY_KEY, $field->getName());
        }
        else{
            $this->pkConstraint->addField($field->getName());
        }
    }

    public function indexKey(Field $field){
        if(is_null($this->keyConstraint)){
            $this->keyConstraint = new InlineConstraint(InlineConstraint::INDEX, $field->getName());
        }
        else{
            $this->keyConstraint->addField($field->getName());
        }
    }

    public function uniqueKey(Field $field){
        if(is_null($this->uniqueConstraint)){
            $this->uniqueConstraint = new InlineConstraint(InlineConstraint::UNIQUE, $field->getName());
        }
        else{
            $this->uniqueConstraint->addField($field->getName());
        }
    }

    public function addConstraint(Constraint $constraint){
        $this->constraints[] = $constraint;
    }


    /**
     * @Override
     * @return array
     */
    public function statement()
    {
        $fieldsDescribed = array_map(function(Field $e){
            return $e->statement();
        }, $this->fields);

        if($this->pkConstraint instanceof InlineConstraint) $fieldsDescribed[] = $this->pkConstraint->statement();
        if($this->keyConstraint instanceof InlineConstraint) $fieldsDescribed[] = $this->keyConstraint->statement();
        if($this->uniqueConstraint instanceof InlineConstraint) $fieldsDescribed[] = $this->uniqueConstraint->statement();

        $fieldsSql = "\t" . implode(",\n\t",$fieldsDescribed);
        $tableSql = "CREATE TABLE {$this->name} (\n {$fieldsSql} \n) ENGINE=InnoDB;";

        $transaction = array_merge([$tableSql], array_map(function(Constraint $c){
            return $c->statement();
        }, $this->constraints));

        return $transaction;
    }

    /**
     * @Override
     * @return array
     */
    public function schema(): array
    {
        return [
            "table" => $this->name,
            "prefix" => $this->prefix ?: NULL,
            "fields" => array_map(function(Field $e){
                return $e->schema();
            },$this->fields)
        ];
    }

}