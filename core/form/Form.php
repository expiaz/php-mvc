<?php

namespace Core\Form;

use Core\Mvc\Entity\Entity;

abstract class Form{

    public static function build(Entity $o, $opts){
        $htmlFields = [];
        $fields = get_class_vars(get_class($o));

        foreach ($fields as $prop => $value){
            $value = $o->$prop;
            if(!is_array($value)){
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

}