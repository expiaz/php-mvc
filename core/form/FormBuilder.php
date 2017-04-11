<?php

namespace Core\Form;

use Core\Exception\NotConformModelException;
use Core\Mvc\Model\Model;

abstract class FormBuilder{

    public static function buildFromEntity(Entity $o){
        if(!property_exists(get_class($o), '_schema')){
            throw new NotConformModelException("Please use ORM and CLI to build your entites from the database : 'php cli.php migrate [<tableName>]'");
        }

        if(is_null($o->getId()))
            return static::buildFromEmptyEntity($o);

        return static::buildFromExistingEntity($o);
    }

    public static function buildFromEmptyEntity(Entity $o){
        $fieldCollection = [];
        $BaseProperties = get_class_vars(get_class($o));
        $schema = $o->_schema;

        foreach ($schema as $field){
            $f = (new Field())
                ->type($field['type'])
                ->name($field['name']);

            switch($field['type']){
                case 'hidden':
                    if(!is_null($BaseProperties[$field['name']]))
                        $f->value($BaseProperties[$field['name']]);
                    elseif(!is_null($field['value']))
                        $f->value($field['value']);
                    $f->required();
                    break;
                case 'select':
                    $f->placeholder($field['name'])
                        ->id($field['name'])
                        ->label();
                    foreach ($field['childs'] as $child){
                        $selected = !is_null($BaseProperties[$field['name']]) && $BaseProperties[$field['name']]
                                ? true
                                : !is_null($field['value']) && $field['value'] == $child['value']
                                    ? true
                                    : false;
                        $f->option($child['value'], $child['name'], $selected);
                    }
                    if($field['required'])
                        $f->required();
                    break;
                default:
                    $f->placeholder($field['name'])
                        ->id($field['name'])
                        ->label();

                    if(!is_null($BaseProperties[$field['name']]))
                        $f->value($BaseProperties[$field['name']]);
                    elseif(!is_null($field['value']))
                        $f->value($field['value']);

                    if($field['required'])
                        $f->required();
                    if(isset($field['maxlength']))
                        $f->maxlength($field['maxlength']);

                    break;
            }

            $fieldCollection[] = $f;
        }
        return new Form($fieldCollection, $o);
    }

    public static function buildFromExistingEntity(Entity $o){
        $fieldCollection = [];
        $BaseProperties = get_class_vars(get_class($o));
        $ObjectProperties = get_object_vars($o);
        $schema = $o->_schema;

        foreach ($schema as $field){
            $f = (new Field())
                ->type($field['type'])
                ->name($field['name']);

            switch($field['type']){
                case 'hidden':
                    if(!is_null($ObjectProperties[$field['name']]))
                        $f->value($ObjectProperties[$field['name']]);
                    elseif(!is_null($BaseProperties[$field['name']]))
                        $f->value($BaseProperties[$field['name']]);
                    elseif(!is_null($field['value']))
                        $f->value($field['value']);
                    $f->required();
                    break;
                case 'select':
                    $f->placeholder($field['name'])
                        ->id($field['name'])
                        ->label();
                    foreach ($field['childs'] as $child){
                        $selected = !is_null($ObjectProperties[$field['name']]) && $ObjectProperties[$field['name']] == $child['value']
                            ? true
                            : (!is_null($BaseProperties[$field['name']]) && $BaseProperties[$field['name']]
                                ? true
                                : (!is_null($field['value']) && $field['value'] == $child['value']
                                    ? true
                                    : false));
                        $f->option($child['value'], $child['name'], $selected);
                    }
                    if($field['required'])
                        $f->required();
                    break;
                default:
                    $f->placeholder($field['name'])
                        ->id($field['name'])
                        ->label();

                    if(!is_null($ObjectProperties[$field['name']]))
                        $f->value($ObjectProperties[$field['name']]);
                    elseif(!is_null($BaseProperties[$field['name']]))
                        $f->value($BaseProperties[$field['name']]);
                    elseif(!is_null($field['value']))
                        $f->value($field['value']);

                    if($field['required'])
                        $f->required();
                    if(isset($field['maxlength']))
                        $f->maxlength($field['maxlength']);

                    break;
            }

            $fieldCollection[] = $f;
        }
        return new Form($fieldCollection, $o);
    }

    public static function buildFromScratch(){
        return new Form();
    }

}