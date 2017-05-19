<?php

namespace Core\Form;

use Core\Database\Orm\Schema\Constraint;
use Core\Database\Orm\Schema\Table;
use Core\Facade\Contracts\DatabaseFacade;
use Core\Form\Field\AbstractField;
use Core\Form\Field\AbstractInputField;
use Core\Form\Field\Input\BooleanInput;
use Core\Form\Field\Input\DateInput;
use Core\Form\Field\Input\FileInput;
use Core\Form\Field\Input\HiddenInput;
use Core\Form\Field\Input\NumberInput;
use Core\Form\Field\Input\PasswordInput;
use Core\Form\Field\Input\TextInput;
use Core\Form\Field\Select\OptionField;
use Core\Form\Field\SelectField;
use Core\Form\Field\TextareaField;
use Core\Mvc\Model\Model;

final class FormBuilder{

    public function &getFormSchema(Table $table){

        $baseSchema = $table->schema();

        $fields = [];

        foreach ($baseSchema['fields'] as $field){

            $description = null;
            if(preg_match('#char|text#i',$field['formtype'])){
                if(! is_null($field['length']) && $field['length'] > 200){
                    $description = new TextareaField();
                } else {
                    $description = new TextInput();
                }
            }
            elseif(preg_match('#date|time#i',$field['formtype'])){
                $description = new DateInput();
            }
            elseif(preg_match('#file#i',$field['formtype'])){
                $description = new FileInput();
            }
            elseif(preg_match('#boolean#i',$field['formtype'])){
                $description = new BooleanInput();
            }
            elseif(preg_match('#password#i',$field['formtype'])){
                $description = new PasswordInput();
            }
            else{
                $description = new NumberInput();
            }

            $description->label($field['name']);
            $description->name($field['name']);

            if($description->getFieldType() === AbstractField::INPUT)
                $description->placeholder($field['name']);


            if(! is_null($field['length']) ){
                switch ($description->getFieldType()){
                    case AbstractField::TEXTAREA:
                        $description->maxlength($field['length']);
                        break;
                    case AbstractInputField::INPUT:
                        /*if($description->getType() === AbstractInputField::TEXT){
                            $description->maxlength($field['length']);
                        }
                        if($description->getType() === AbstractInputField::NUMBER){
                            $description->max($field['length']);
                        }*/
                        break;
                }
            }

            if($field['null']){
                $description->required();
            }

            if(! is_null($field['default'])){
                if($field['default'] === 'NOT NULL'){
                    $description->required();
                }
                elseif($field['default'] === 'NULL'){

                }
                else{
                    $description->value($field['default']);
                }
            }


            foreach ($field['constraints'] as $constraint){
                switch ($constraint['type']){

                    case Constraint::PRIMARY_KEY:
                        if($field['auto']){
                            $description = new HiddenInput();
                            //$description->label($field['name']);
                            $description->name($field['name']);

                            if(! is_null($field['default'])){
                                if($field['default'] === 'NOT NULL'){

                                }
                                elseif($field['default'] === 'NULL'){

                                }
                                else{
                                    $description->value($field['default']);
                                }
                            }

                            if(! is_null($description->getValue())){
                                $description->required();
                            }
                        }
                        break;

                    case Constraint::INDEX:
                    case Constraint::UNIQUE:
                        $description->required();
                        break;


                    case Constraint::ONE_TO_ONE:
                    case Constraint::MANY_TO_ONE:
                    case Constraint::MANY_TO_MANY:

                        $relationnalField = new SelectField();

                        if($constraint['type'] === Constraint::MANY_TO_MANY){
                            $relationnalField->label($constraint['table']);
                            $relationnalField->name($constraint['table']);
                            $relationnalField->data('table', $constraint['table']);
                            $relationnalField->data('field', $constraint['field']);
                            $relationnalField->data('constraint', $field['name']);
                            $relationnalField->multiple();
                            $fields[$relationnalField->getName()] = $relationnalField;
                        } else {
                            $relationnalField->label($description->getLabel());
                            $relationnalField->required();
                            $relationnalField->name($description->getName());
                            $relationnalField->value($description->getValue());
                            $description = $relationnalField;
                        }


                        //$thisTable = $baseSchema['table'];
                        //$thisField = "{$thisTable}.{$field['name']}";
                        $constraintTable = $constraint['table'];
                        $constraintField = "{$constraintTable}.{$constraint['field']}";
                        //$relationnalTable = "{$thisTable}_{$constraintTable}";
                        //$relationnalThisField = "{$relationnalTable}.{$thisField}_id";
                        //$relationnalConstraintField = "{$relationnalTable}.{$constraintField}_id";
                        $formField =  "{$constraintTable}." . ($constraint['form'] ?? $constraint['field']);

                        $sql = sprintf("SELECT %s AS id, %s AS content FROM %s;", $constraintField, $formField, $constraintTable);
                        $opts = DatabaseFacade::raw($sql);

                        //getall
                        //"SELECT {$constraintField} AS id, {$formField} AS content FROM {$constraintTable}";

                        //hydrate
                        //"SELECT {$constraintField} AS id, {$formField} AS content FROM {$thisTable}, {$constraintTable}, {$relationnalTable} WHERE {$thisField} = {$relationnalThisField} AND {$constraintField} = {$relationnalConstraintField} AND {$thisField} = {$field['value']}";

                        foreach($opts as $o){

                            $optionField = (new OptionField())
                                ->value($o->id)
                                ->content($o->content);

                            if($relationnalField->getValue() === $o->id){
                                $optionField->selected();
                            }

                            $relationnalField->option($optionField);
                        }

                        break;
                }
            }

            $fields[$description->getName()] = $description;
        }

        return $fields;
    }

    public function build(Model $model)
    {
        $fieldsCollection = $this->getFormSchema($model->getSchema()->table());

        return $this->hydrate($fieldsCollection, $model);
    }

    private function hydrate(array &$fieldCollection, Model $model){

        //$schema = $model->getSchema()->schema();

        foreach ($fieldCollection as $fieldName => $formField){
            $value = $model->{'get' . ucfirst($fieldName)}();
            //$schemaField = $schema['fields'][$fieldName] ?? [];

            if($formField instanceof FileInput){
                //FIXME: nothing or it'll re-upload
            } else {
                $formField->bindEntry($value);
            }



            /*
            if(is_array($value) && $formField->getFieldType() === AbstractField::SELECT){

                foreach ($formField->getOptions() as $option) {
                    if ($option->isSelected()) {
                        $option->selected(false);
                    }
                }

                foreach ($value as $optionSelected){
                    foreach ($formField->getOptions() as $option){
                        if($option->getValue() === $optionSelected){
                            $option->selected();
                        }
                    }
                }
            } else{
                $formField->value($value);
            }
            */

            /*
            if(!is_array($value) || ! ($formField->getFieldType() === AbstractField::SELECT) || is_null($formField->getData('table')) || is_null($formField->getData('field')) || is_null($formField->getData('constraint')) ){
                $formField->value($value);
                next($fieldCollection);
            }

            $thisTable = $schema['table'];
            $thisField = "{$thisTable}.{$formField->getData('constraint')}";
            $constraintTable = $formField->getData('table');
            $constraintField = "{$constraintTable}.{$formField->getData('field')}";
            $relationnalTable = "{$thisTable}_{$constraintTable}";
            $relationnalThisField = "{$relationnalTable}.{$thisField}_id";
            $relationnalConstraintField = "{$relationnalTable}.{$constraintField}_id";

            $sql = sprintf(
                "SELECT %s AS id FROM %s, %s, %s WHERE %s = %s AND %s = %s AND %s = %s",
                $constraintField,
                $thisTable,
                $constraintTable,
                $relationnalTable,
                $thisField,
                $relationnalThisField,
                $constraintField,
                $relationnalConstraintField,
                $thisField,
                $value
            );*/

        }

        return new Form($fieldCollection, $model);
    }

}