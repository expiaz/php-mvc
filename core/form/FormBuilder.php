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
                            $description->label($field['name']);
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

                        $lastValue = $description->getValue();
                        $description = new SelectField();

                        $description->required();
                        $description->name($constraint['table']);
                        $description->value($lastValue);

                        if($constraint['type'] == Constraint::MANY_TO_MANY || $constraint['type'] == Constraint::ONE_TO_MANY){
                            $description->multiple();
                        }

                        $content = $constraint['form'] ?? $constraint['field'];
                        $sql = "SELECT {$constraint['field']} AS option, {$content} AS content FROM {$constraint['table']};";
                        $opts = DatabaseFacade::raw($sql);

                        foreach($opts as $o){

                            $optionField = (new OptionField())
                                ->value($o->option)
                                ->content($o->content);

                            if($description->getValue() === $o->option){
                                $optionField->selected();
                            }

                            $description->option($optionField);
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

        $schema = $model->getSchema()->schema();

        foreach ($schema['fields'] as $field){
            $value = $model->{'get' . ucfirst($field['name'])}();
            $input = $fieldCollection[$field['name']];
            $input->value($value);
        }

        return new Form($fieldCollection, $model);
    }

}