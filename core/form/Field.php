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

    public function getType(){
        return $this->type;
    }


    public function name($name){
        $this->name = (string) $name;
        return $this;
    }

    public function getName(){
        return $this->name;
    }


    public function value($value){
        $this->value = $value;
        return $this;
    }

    public function getValue(){
        return $this->value;
    }


    public function placeholder($placeholder){
        $this->placeholder = (string) $placeholder;
        return $this;
    }

    public function getPlaceholder(){
        return $this->placeholder;
    }


    public function required(){
        $this->required = true;
        return $this;
    }

    public function isRequired(){
        return $this->required;
    }


    public function id($id){
        $this->id = (string) $id;
        return $this;
    }

    public function getId(){
        return $this->id;
    }


    public function class($class): Field
    {
        $this->class = (string) $class;
        return $this;
    }

    public function getClass()
    {
        return $this->class;
    }


    public function focus(): Field
    {
        $this->focus = true;
        return $this;
    }

    public function isFocused()
    {
        return $this->focus;
    }


    public function multiple(): Field
    {
        $this->multiple = true;
        return $this;
    }

    public function isMultiple(){
        return $this->multiple;
    }


    public function label($label = null): Field
    {
        $this->label = $label ?? $this->name ?? 'label';
        return $this;
    }

    public function getLabel(){
        return $this->label;
    }


    public function option($value, $content, $selected = false): Field
    {
        $this->childs[] = [
            'value' => $value,
            'content' => $content,
            'selected' => $selected
        ];
        return $this;
    }

    public function getOptions(){
        return $this->childs;
    }


    public function maxlength($length): Field
    {
        $this->maxlength = (int) $length;
        return $this;
    }

    public function minlength($length): Field
    {
        $this->minlength = (int) $length;
        return $this;
    }


    public function create(): string
    {

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
            case 'boolean':
                $out .= $this->buildBoolean();
                break;
            default:
                $out .= $this->buildInput();
                break;
        }

        return $out;
    }

    private function buildInput(): string
    {

        $out = "<input";

        if(! is_null($this->type))
            $out .= " type=\"{$this->type}\"";

        if(! is_null($this->name)){
            if($this->type === 'submit')
                $out .= " name=\"submit\"";
            else
                $out .= " name=\"{$this->name}\"";
        }
        else
            $out .= " name=\"submit\"";

        if(! is_null($this->id))
            $out .= " id=\"{$this->id}\"";

        if(! is_null($this->value))
            $out .= " value=\"{$this->value}\"";

        if(! is_null($this->placeholder))
            $out .= " placeholder=\"{$this->placeholder}\"";

        if(! is_null($this->class))
            $out .= " class=\"{$this->class}\"";

        if($this->required)
            $out .= " required";

        if(! is_null($this->maxlength))
            $out .= " maxlength=\"{$this->maxlength}\"";

        $out .= "/>";

        return $out;
    }

    private function buildSelect(): string
    {
        $out = "<select";
        if(! is_null($this->name))
            $out .= " name=\"{$this->name}\"";

        if(! is_null($this->id))
            $out .= " id=\"{$this->id}\"";

        if(! is_null($this->class))
            $out .= " class=\"{$this->class}\"";

        if($this->required)
            $out .= " required";

        $out .= ">";

        foreach ($this->childs as $option){
            if($option['value'] === $this->value){
                $option['selected'] = true;
            }
            $out .= $this->buildOption($option);
        }

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

    private function buildTextarea(): string
    {
        $out = "<textarea";

        if(! is_null($this->name))
            $out .= " name=\"{$this->name}\"";

        if(! is_null($this->id))
            $out .= " id=\"{$this->id}\"";

        if(! is_null($this->class))
            $out .= " class=\"{$this->class}\"";

        if($this->required)
            $out .= " required";

        if(! is_null($this->maxlength))
            $out .= " cols=\"10\" rows=\"5\"";

        $out .= "/>";

        if(! is_null($this->value))
            $out .= $this->value;

        $out .= "</textarea>";
        return $out;
    }

    private function buildBoolean(): string
    {
        $false = "<input type=\"radio\" value=\"0\" name=\"{$this->name}\" ";
        $true = "<input type=\"radio\" value=\"1\" name=\"{$this->name}\" ";
        if(! is_null($this->id)) {
            $false .= " id=\"{$this->id}_false\"";
            $true .= " id=\"{$this->id}_true\"";
        }
        if(! is_null($this->class)){
            $false .= " class=\"{$this->class}\"";
            $true .= " class=\"{$this->class}\"";
        }
        if(! is_null($this->required)){
            $false .= " required";
            $true .= " required";
        }

        $false .= "/> No";
        $true .= " checked/> Yes";
        return $true . $false;
    }

    public function validateEntry($entry): bool
    {
        return is_null($entry) ? $this->required ? false : true : true;
    }

}