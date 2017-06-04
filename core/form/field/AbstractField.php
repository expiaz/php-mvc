<?php

namespace Core\Form\Field;

abstract class AbstractField{

    const INPUT = 'input';
    const SELECT = 'select';
    const TEXTAREA = 'textarea';

    //base
    protected $field; //type
    protected $label;
    protected $name;
    protected $value;

    //sweet
    protected $required;
    protected $disabled;

    //attrs
    protected $id;
    protected $class;
    protected $data;

    protected $focus;
    protected $inline;

    protected $before;
    protected $after;

    protected function __construct($fieldType)
    {
        $this->field = $fieldType;
        $this->label = null;
        $this->data = [];
        $this->required = false;
        $this->disabled = false;
        $this->focus = false;
        $this->name = null;
        $this->value = null;
        $this->inline = false;
        $this->before = "";
        $this->after = "";
    }

    public function appendBefore(string $data){
        return $this->before($data);
    }

    public function prependBefore(string $data){
        $this->before = $data . $this->before;
        return $this;
    }

    public function before(string $data){
        $this->before .= $data;
        return $this;
    }

    public function getBefore(){
        return $this->before;
    }


    public function appendAfter(string $data){
        return $this->after($data);
    }

    public function prependAfter(string $data){
        $this->after = $data . $this->after;
        return $this;
    }

    public function after(string $data){
        $this->after .= $data;
        return $this;
    }

    public function getAfter(){
        return $this->after;
    }


    public function getFieldType(){
        return $this->field;
    }

    public function name($name){
        $this->name = (string) $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function value($value){
        $this->value = $value;
        return $this;
    }

    public function getValue(){
        return $this->value;
    }


    public function data($name, $value){
        $this->data[$name] = $value;
        return $this;
    }

    public function getData($name = null){
        if(! is_null($name)){
            return $this->data[$name];
        }
        return $this->data;
    }


    public function label($label = null): AbstractField
    {
        $this->label = $label ?? $this->name ?? 'label';
        return $this;
    }

    public function getLabel(){
        return $this->label;
    }


    public function required(){
        $this->required = true;
        return $this;
    }

    public function isRequired(){
        return $this->required;
    }


    public function disabled(){
        $this->disabled = true;
        return $this;
    }

    public function isDisabled(){
        return $this->disabled;
    }


    public function id($id){
        $this->id = (string) $id;
        return $this;
    }

    public function getId(){
        return $this->id;
    }


    public function class($class): AbstractField
    {
        $this->class = (string) $class;
        return $this;
    }

    public function getClass()
    {
        return $this->class;
    }


    public function focus(): AbstractField
    {
        $this->focus = true;
        return $this;
    }

    public function isFocused(): bool
    {
        return $this->focus;
    }

    public function inline($in = true){
        $this->inline = (bool) $in;
        return $this;
    }

    public function isInline(){
        return $this->inline;
    }



    protected function buildLabel(): string{
        if(! is_null($this->label)){
            $this->id = ! is_null($this->id) ? $this->id : $this->name;
            return "<label for=\"{$this->id}\">{$this->label}</label>" . ($this->inline ? "" : "<br/>");
        }
        return '';
    }

    protected function buildCommonProps(){
        $out = '';

        if(! is_null($this->name)){
            $out .= " name=\"{$this->name}\"";
        }

        if(! is_null($this->value) && ! is_array($this->value)){
            $out .= " value=\"{$this->value}\"";
        }

        if(! is_null($this->id))
            $out .= " id=\"{$this->id}\"";

        if(! is_null($this->class))
            $out .= " class=\"{$this->class}\"";

        if($this->required)
            $out .= " required";

        if($this->disabled)
            $out .= " disabled";

        if($this->focus){
            $out .= " autofocus";
        }

        foreach ($this->data as $data => $value){
            $out .= " data-{$data}=\"{$value}\"";
        }

        return $out;
    }

    abstract function build(): string;

    protected function addLabel(string $htmlField): string
    {
        return $this->buildLabel() . $htmlField;
    }

    public function bindEntry($entry){
        $this->value($entry);
    }

    public function validateEntry($entry): bool{

        if(is_null($entry)){
            if($this->required){
                return false;
            }
            return true;
        }

        return true;
    }

}