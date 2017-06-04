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


        $out = "<select";

        if(! is_null($this->name)){
            if($this->multiple){
                $out .= " name=\"{$this->name}[]\"";
            } else{
                $out .= " name=\"{$this->name}\"";
            }
        }

        if($this->multiple)
            $out .= " multiple";

        if(! is_null($this->size))
            $out .= " size=\"{$this->size}\"";

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

    public function value($value){

        $this->value = $value;

        if(is_array($value)){
            foreach ($this->options as $option){
                foreach ($value as $e){
                    if($option->getValue() == $e){
                        $option->selected();
                    }
                }
            }
            return;
        }

        foreach ($this->options as $option){
            if($option->getValue() == $value){
                $option->selected();
            }
        }

        return $this;
    }

    public function bindEntry($entry)
    {
        $this->value($entry);
    }

    public function validateEntry($entry): bool
    {
        if(is_null($entry)){
            if($this->required){
                return false;
            }
            return true;
        }

        if(is_array($entry)){
            foreach ($this->options as $option){
                foreach ($entry as $e){
                    if($option->getValue() == $e){
                        $option->selected();
                        return true;
                    }
                }
            }
            return false;
        }

        foreach ($this->options as $option){
            if($option->getValue() == $entry){
                return true;
            }
        }

        return false;
    }

}