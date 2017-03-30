<?php

namespace Core\Form;

class Form{

    private $fields;
    private $class;
    private $id;
    private $enctype;
    private $action;
    private $method;
    private $hasSubmitButton;

    public function __construct($fieldCollection = [])
    {
        $this->id = null;
        $this->class = null;
        $this->enctype = null;
        $this->method = null;
        $this->hasSubmitButton = false;
        $this->action = null;
        $this->fields = [];

        foreach ($fieldCollection as $field)
            $this->field($field);
    }

    public function field(Field $field){

        if($field->type === 'submit')
            $this->hasSubmitButton = true;

        $this->fields[] = $field;
        return $this;
    }

    public function class($class){
        $this->class = $class;
        return $this;
    }

    public function id($id){
        $this->id = $id;
        return $this;
    }

    public function enctype($enctype){
        switch($enctype){
            case 'file':
                $this->enctype = "multipart/form-data";
                break;
            default:
                $this->enctype = "multipart/form-data";
                break;
        }
        return $this;
    }

    public function action($action){
        $this->action = $action;
        return $this;
    }

    public function method($method){
        $this->method = $method;
        return $this;
    }

    public function build(){
        $out = '<form';
        if($this->method)
            $out .= " method=\"{$this->method}\"";
        else
            $out .= " method=\"POST\"";
        if($this->action)
            $out .= " action=\"{$this->action}\"";
        else
            $out .= " action=\"\"";
        if($this->enctype)
            $out .= " enctype=\"{$this->enctype}\"";
        if($this->class){
            $out .= " class=\"{$this->class}\"";
        }
        if($this->id){
            $out .= " id=\"{$this->id}\"";
        }
        $out .= ">";

        $out .= implode('<br/>', array_map(function($f){
            return $f->create();
        }, $this->fields));

        if(!$this->hasSubmitButton)
            $out .= "<br/><input type=\"submit\" value=\"submit\"/>";
        $out .= "</form>";
        return $out;
    }

}