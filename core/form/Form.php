<?php

namespace Core\Form;

use Core\Mvc\Entity\Entity;

abstract class Form{

    public static function buildFromEntity(Entity $o, $opts){
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
                        ->required(true);
                }
                $htmlFields[] = $f->create(true);
            }
        }

        $out = "<form";
        $sample = $opts['method'] ?? 'POST';
        $out .= " method=\"{$sample}\"";
        $sample = $opts['action'] ?? '';
        $out .= " action=\"{$sample}\"";
        if(isset($opts['class'])){
            $out .= " class=\"{$opts['class']}\"";
        }
        if(isset($opts['id'])){
            $out .= " id=\"{$opts['id']}\"";
        }
        $out .= ">";
        $out .= implode('',$htmlFields);
        $out .= "<br/><input type=\"submit\" value=\"submit\"/></form>";

        return $out;
    }

    public static function buildFromTable($tableName){

    }

    public static function buildFromScratch(array $fields, $opts){

        $out = "<form";
        $sample = $opts['method'] ?? 'POST';
        $out .= " method=\"{$sample}\"";
        $sample = $opts['action'] ?? '';
        $out .= " action=\"{$sample}\"";
        if(isset($opts['class'])){
            $out .= " class=\"{$opts['class']}\"";
        }
        if(isset($opts['id'])){
            $out .= " id=\"{$opts['id']}\"";
        }
        $out .= ">";
        $out .= implode('',array_map(function($f){ return $f->create(true); },$fields));
        $out .= "<br/><input type=\"submit\" value=\"submit\"/></form>";

        return $out;
    }

}