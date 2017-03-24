<?php

namespace Core\Form;

class Field{

    private $type;
    private $name;
    private $value;
    private $placeholder;
    private $required;
    private $id;
    private $class;

    public function __construct($type = 'text', $name = 'input', $value = null, $placeholder = null, $required = false, $id = null, $class = null)
    {
        $this->type = $type;
        $this->name = $name;
        $this->value = $value;
        $this->placeholder = $placeholder;
        $this->required = $required;
        $this->id = $id;
        $this->class = $class;
    }

    public function type($type){
        $this->type = $type;
        return $this;
    }

    public function name($name){
        $this->name = $name;
        return $this;
    }

    public function value($value){
        $this->value = $value;
        return $this;
    }

    public function placeholder($placeholder){
        $this->placeholder = $placeholder;
        return $this;
    }

    public function required($required){
        $this->required = $required;
        return $this;
    }

    public function id($id){
        $this->id = $id;
        return $this;
    }

    public function class($class){
        $this->class = $class;
        return $this;
    }

    public function create($label){
        $out = '';

        if($this->type !== 'hidden')
            $out .= '<br/>';

        if($label && $this->id !== null){
            $out .= "<label for=\"{$this->id}\">{$this->name}</label><br/>";
        }

        $out .= "<input";
        if($this->type)
            $out .= " type=\"{$this->type}\"";
        if($this->name)
            $out .= " name=\"{$this->name}\"";
        if($this->value)
            $out .= " value=\"{$this->value}\"";
        if($this->placeholder)
            $out .= " placeholder=\"{$this->placeholder}\"";
        if($this->class)
            $out .= " class=\"{$this->class}\"";
        if($this->id)
            $out .= " id=\"{$this->id}\"";
        if($this->required)
            $out .= " required";
        $out .= "/>";
        return $out;
    }

}