<?php

namespace Core\Database\Orm\Schema;

use Core\Database\Orm\Schema\Constraint\InlineConstraint;

class Table implements Statementizable, Schematizable {

    private $fields;
    private $prefix;
    private $name;
    private $defaultSelection;

    private $constraints;
    private $pkConstraint;
    private $keyConstraint;
    private $uniqueConstraint;

    private $constraintFields;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->fields = [];
        $this->constraints = [];
        $this->constraintFields = [];
    }

    public function getName(){
        return $this->name;
    }

    public function prefix($prefix): Table{
        $this->prefix = $prefix;
        return $this;
    }

    public function getPrefix(){
        return $this->prefix;
    }

    public function field($name): Field{
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
        /*if($constraint->getType() === Constraint::MANY_TO_MANY){
            $f = new Field($this, $constraint->getName());
            $f->isConstraint();
            $this->constraintFields[] = $f;
        }*/
    }

    public function defaultSelection(Field $f){
        $this->defaultSelection = $f;
    }

    public function getDefaultSelection():Field{
        return $this->defaultSelection ?? $this->primaryKey->getFields()[0];
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

        $constraintsSql = array_map(function(Constraint $c){
            return $c->statement();
        }, $this->constraints);

        $transaction = array_merge([$tableSql], ... $constraintsSql);

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
            "prefix" => $this->prefix ?? NULL,
            "fields" => array_map(function(Field $e){
                return $e->schema();
            },$this->fields)
        ];
    }

    public function __toString()
    {
        return $this->name;
    }

}