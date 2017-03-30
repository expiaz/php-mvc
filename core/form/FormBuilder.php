<?php

namespace Core\Form;

use Core\Mvc\Entity\Entity;

abstract class FormBuilder{

    public static function buildFromEntity(Entity $o, $opts = []){
        if(!property_exists(get_class($o), '_schema')){
            self::legacyBuildFromEntity($o,$opts);
        }

        $htmlFields = [];
        $props = get_class_vars(get_class($o));
        $schema = $o->_schema;

        foreach ($schema as $field){

            $f = new Field();

            $f->type($field['type'])
                ->name($field['name']);

            switch($field['type']){
                case 'hidden':
                    $f->value($field['value']);
                    $f->required();
                    break;
                case 'select':
                    foreach ($field['childs'] as $child)
                        $f->option($child['value'], $child['name']);
                default:
                    $f->placeholder($field['name'])
                        ->id($field['name'])
                        ->label();

                    $p = $field['name'];
                    if($o->$p)
                        $f->value($o->$p);
                    elseif($field['value'])
                        $f->value($field['value']);
                    elseif($props[$p])
                        $f->value($props[$p]);

                    if($field['required'])
                        $f->required();
                    if(isset($field['maxlength']))
                        $f->maxlength($field['maxlength']);
                    break;
            }

            $htmlFields[] = $f;
        }

        return new Form($htmlFields);
    }

    private static function legacyBuildFromEntity(Entity $o, $opts = []){
        $htmlFields = [];
        $fields = get_class_vars(get_class($o));

        foreach ($fields as $prop => $value){
            $value = $o->$prop ?? $value;
            if(!is_array($value) && !is_object($value) && $prop{0} !== '_'){
                $f = new Field();
                if($prop === 'id'){
                    $f->type('hidden')
                        ->value($value)
                        ->required(true)
                        ->name($prop);
                }
                else{
                    $f->type('text')
                        ->id($prop)
                        ->placeholder($prop)
                        ->name($prop)
                        ->value($value)
                        ->required(true)
                        ->label();
                }
                $htmlFields[] = $f;
            }
        }

        return new Form($htmlFields);
    }

    public static function buildFromScratch(){
        return new Form();
    }

}