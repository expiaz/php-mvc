<?php

namespace Core\Form;

use Closure;
use Core\Database\Database;
use Core\Database\Orm\Schema\Constraint;
use Core\Database\Orm\Schema\Table;
use Core\Facade\Contracts\DatabaseFacade;
use Core\Mvc\Model\Model;

final class FormBuilder{

    public function &toFormSchema(&$baseSchema, Table $table){
        $fields = [];
        foreach ($baseSchema['fields'] as $field){
            $description = [];

            $description['name'] = $field['name'];

            if(preg_match('#char|text#i',$field['formtype'])){
                $description['type'] = 'text';
            }
            elseif(preg_match('#date|time#i',$field['formtype'])){
                $description['type'] = 'date';
            }
            elseif(preg_match('#file#i',$field['formtype'])){
                $description['type'] = 'file';
            }
            elseif(preg_match('#boolean#i',$field['formtype'])){
                $description['type'] = 'boolean';
            }
            else{
                $description['type'] = 'number';
            }

            if(! is_null($field['length'])){
                $description['maxlength'] = $field['length'];
                if($field['length'] > 200 && $description['type'] == 'text')
                    $description['type'] = 'textarea';
            }
            else{
                $description['maxlength'] = null;
            }


            $description['required'] = $field['null'] ? false : true;

            if(! is_null($field['default'])){
                if($field['default'] === 'NOT NULL'){
                    $description['required'] = true;
                    $description['value'] = null;
                }
                elseif($field['default'] === 'NULL'){
                    $description['required'] = false;
                    $description['value'] = null;
                }
                else{
                    $description['value'] = $field['default'];
                }
            }
            else{
                $description['value'] = NULL;
            }


            foreach ($field['constraints'] as $constraint){
                switch ($constraint['type']){

                    case Constraint::PRIMARY_KEY:
                        if($field['auto']){
                            $description['type'] = 'hidden';
                            if(! is_null($description['value'])){
                                $description['required'] = true;
                            }
                            else{
                                $description['required'] = false;
                            }
                        }
                        break;
                    case Constraint::INDEX:
                    case Constraint::UNIQUE:
                        $description['required'] = true;
                        break;


                    case Constraint::ONE_TO_ONE:
                    case Constraint::MANY_TO_ONE:
                    case Constraint::MANY_TO_MANY:
                        $constraintField = [];
                        $constraintField['required'] = true;
                        $constraintField['name'] = $constraint['table'];
                        $constraintField['value'] = null;
                        $constraintField['maxlength'] = null;
                        $constraintField['type'] = 'select';
                        if($constraint['type'] == Constraint::MANY_TO_MANY) $constraintField['multiple'] = true;
                        else $constraintField['multiple'] = false;
                        $content = $table->getDefaultSelection()->getName();
                        $sql = "SELECT {$constraint['field']} AS option, title AS content FROM {$constraint['table']};";
                        $opts = DatabaseFacade::raw($sql);
                        $constraintField['options'] = [];
                        foreach($opts as $o){
                            $constraintField['options'][] = ["value" => $o->option, "content" => $o->content, 'selected' => $description['value'] && $description['value'] == $o->option ? true : false];
                        }
                        $fields[] = $constraintField;
                        break;
                }
            }

            $fields[] = $description;
        }
        return $fields;
    }

    public function build(Model $model){
        $schema = $model->getSchema()->schema();
        $fields = $this->toFormSchema($schema, $model->getSchema()->table());
        $fieldsCollection = [];

        foreach ($fields as $field){
            $f = new Field();
            $f->name($field['name']);
            $f->type($field['type']);
            $f->class($field['name']);
            $f->id($field['name']);
            if($field['required'])
                $f->required();
            if(!is_null($field['value']))
                $f->value($field['value']);
            if(!is_null($field['maxlength']))
                $f->maxlength($field['maxlength']);
            if(isset($field['multiple']) && $field['multiple'])
                $f->multiple();
            if(isset($field['options']) && is_array($field['options']))
                foreach ($field['options'] as $option)
                    $f->option($option['value'], $option['content'], $option['selected']);
            $f->label();
            $f->placeholder($field['name']);
            $fieldsCollection[$field['name']] = $f;
        }
        $fieldsCollection[array_keys($fieldsCollection)[0]]->focus();
        return $this->hydrate($fieldsCollection, $model);
    }

    private function hydrate(array &$fieldCollection, Model $model){
        foreach ($model->getModifications() as $field){
            $fieldCollection[$field]->value($model->{'get' . ucfirst($field)}());
        }
        return new Form($fieldCollection, $model);
    }

}