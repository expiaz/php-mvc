<?php

namespace Core\Form\Field;

use Core\Form\Field\Select\OptionField;

class SelectField extends AbstractField {

    protected $multiple;
    protected $options;
    protected $size;

    public function __construct()
    {
        parent::__construct(AbstractField::SELECT);
        $this->multiple = false;
        $this->options = [];
        $this->size = null;
    }

    public function option(OptionField $option){
        $this->options[] = $option;
        return $this;
    }

    public function getOptions(){
        return $this->options;
    }


    public function size($size){
        $this->size = $size;
        return $this;
    }

    public function getSize(){
        return $this->size;
    }


    public function multiple(){
        $this->multiple = true;
        return $this;
    }

    public function isMultiple(){
        return $this->multiple;
    }

    public function build(): string{

        $baseProps = $this->buildCommonProps();

        $out = "<select";

        if($this->multiple)
            $out .= " multiple";

        if(! is_null($this->size))
            $out .= " size=\"{$this->size}\"";

        $out .= $baseProps;

        $out .= ">";

        foreach ($this->options as $option){
            if($option->getValue() === $this->value){
                $option->selected();
            }

            $out .= $option->build();
        }

        $out .= "</select>";

        return $this->addLabel($out);

    }

    public function validateEntry($entry): bool
    {
        if(is_null($entry)){
            if($this->required){
                return false;
            }
            return true;
        }

        foreach ($this->options as $option){
            if($option->getValue() == $entry){
                return true;
            }
        }

        return false;
    }

}