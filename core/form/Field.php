<?php

namespace Core\Form;

class Field{

    //base
    public $type;
    public $name;
    private $value;
    private $label;

    //sweet
    private $placeholder;
    private $required;

    //attrs
    private $id;
    private $class;

    //select
    private $data;
    private $childs;
    private $focus;
    private $multiple;

    //text input
    private $maxlength;
    private $minlength;

    public function __construct()
    {
        $this->type = 'text';
        $this->name = 'field';
        $this->value = null;
        $this->placeholder = null;
        $this->required = false;
        $this->id = null;
        $this->class = null;
        $this->data = null;
        $this->childs = [];
        $this->focus = false;
        $this->label = null;
    }

    public function type($type){
        $this->type = (string) $type;
        return $this;
    }

    public function name($name){
        $this->name = (string) $name;
        return $this;
    }

    public function value($value){
        $this->value = (string) $value;
        return $this;
    }

    public function placeholder($placeholder){
        $this->placeholder = (string) $placeholder;
        return $this;
    }

    public function required(){
        $this->required = true;
        return $this;
    }

    public function id($id){
        $this->id = (string) $id;
        return $this;
    }

    public function class($class){
        $this->class = (string) $class;
        return $this;
    }

    public function focus(){
        $this->focus = true;
        return $this;
    }

    public function multiple(){
        $this->multiple = true;
        return $this;
    }

    public function label($label = null){
        $this->label = $label ?? $this->name ?? 'label';
    }

    public function option($value, $content, $selected = false){
        $this->childs[] = [
            'value' => $value,
            'content' => $content,
            'selected' => $selected
        ];
    }

    public function maxlength($length){
        $this->maxlength = (int) $length;
    }

    public function minlength($length){
        $this->minlength = (int) $length;
    }

    public function create(){

        $out = '';

        if($this->type !== 'hidden' && $this->type !== 'submit' && $this->label){
            $this->id = $this->id ?? $this->name;
            $out .= "<label for=\"{$this->id}\">{$this->name}</label><br/>";
        }

        switch ($this->type) {
            case 'select':
                $out .= $this->buildSelect();
                break;
            case 'textarea':
                $out .= $this->buildTextarea();
                break;
            default:
                $out .= $this->buildInput();
                break;
        }

        return $out;
    }

    private function buildInput(){
        $out = "<input";
        if($this->type)
            $out .= " type=\"{$this->type}\"";
        if($this->name){
            if($this->type === 'submit')
                $out .= " name=\"submit\"";
            else
                $out .= " name=\"{$this->name}\"";
        }
        else
            $out .= " name=\"submit\"";
        if($this->id)
            $out .= " id=\"{$this->id}\"";
        if($this->value)
            $out .= " value=\"{$this->value}\"";
        if($this->placeholder)
            $out .= " placeholder=\"{$this->placeholder}\"";
        if($this->class)
            $out .= " class=\"{$this->class}\"";
        if($this->required)
            $out .= " required";
        if($this->maxlength)
            $out .= " maxlength=\"{$this->maxlength}\"";
        $out .= "/>";
        return $out;
    }

    private function buildSelect(){
        $out = "<select";
        if($this->name)
            $out .= " name=\"{$this->name}\"";
        if($this->id)
            $out .= " id=\"{$this->id}\"";
        if($this->class)
            $out .= " class=\"{$this->class}\"";
        if($this->required)
            $out .= " required";
        $out .= ">";
        foreach ($this->childs as $option)
            $out .= $this->buildOption($option);
        $out .= "</select>";
        return $out;
    }

    private function buildOption($opts){
        $out =  "<option value=\"{$opts['value']}\"";
        if($opts['selected'])
            $out .= " selected";
        $out .= ">{$opts['content']}</option>";
        return $out;
    }

    private function buildTextarea(){
        $out = "<textarea";
        if($this->name)
            $out .= " name=\"{$this->name}\"";
        if($this->id)
            $out .= " id=\"{$this->id}\"";
        if($this->class)
            $out .= " class=\"{$this->class}\"";
        if($this->required)
            $out .= " required";
        if($this->maxlength)
            $out .= "cols=\"{$this->maxlength}\" rows=\"{$this->maxlength}\"";
        $out .= "/>";
        if($this->value)
            $out .= $this->value;
        $out .= "</textarea>";
    }

    public function validateEntry($entry){
        return is_null($entry) ? $this->required ? false : true : true;
    }

}