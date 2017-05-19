<?php

namespace Core\Form\Field\Select;

class OptionField{

    protected $value;
    protected $content;
    protected $selected;
    protected $disabled;

    public function __construct()
    {
        $this->value = null;
        $this->content = '';
        $this->selected = false;
        $this->disabled = false;
    }

    /**
     * @return null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param null $value
     */
    public function value($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function content(string $content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSelected(): bool
    {
        return $this->selected;
    }

    public function selected($selected = true)
    {
        $this->selected = $selected;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function disabled()
    {
        $this->disabled = true;
        return $this;
    }

    public function build(){

        $out = '<option';

        if(! is_null($this->value)){
            $out .= " value=\"{$this->value}\"";
        }

        if($this->selected){
            $out .= ' selected';
        }

        if($this->disabled){
            $out .= ' disabled';
        }

        $out .= ">{$this->content}</option>";

        return $out;
    }

}