<?php

namespace Core\Form\Field;

abstract class AbstractInputField extends AbstractField{

    const FILE = 'file';
    const TEXT = 'text';
    const NUMBER = 'number';
    const PASSWORD = 'password';
    const DATE = 'date';
    const SUBMIT = 'submit';
    const RESET = 'reset';
    const HIDDEN = 'hidden';
    const BOOLEAN = 'boolean';

    protected $type;
    protected $value;
    protected $pattern;
    protected $title;

    protected $placeholder;

    public function __construct($type)
    {
        parent::__construct(AbstractField::INPUT);
        $this->type = $type;
        $this->value = null;
        $this->placeholder = null;
        $this->pattern = null;
        $this->title = null;
    }

    public function type($type){
        $this->type = $type;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }


    public function value($value){
        $this->value = (string) $value;
        return $this;
    }

    public function getValue(){
        return $this->value;
    }


    public function placeholder($placeholder)
    {
        $this->placeholder = (string) $placeholder;
        return $this;
    }

    public function getPlaceholder()
    {
        return $this->placeholder;
    }


    /**
     * @return mixed
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param mixed $pattern
     */
    public function pattern($pattern)
    {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function title($title)
    {
        $this->title = $title;
        return $this;
    }


    protected function buildLabel(): string
    {
        if($this->type !== 'hidden' && $this->type !== 'submit'){
            return parent::buildLabel();
        }
        return '';
    }

    public function addBaseProps(string $childProps): string{

        $baseProps = $this->buildCommonProps();

        $name = ! is_null($this->name) ? $this->name : $this->type;

        $out = "<input type=\"{$this->type}\" name=\"{$name}\"";

        if(! is_null($this->value)){
            $out .= " value=\"{$this->value}\"";
        }

        if(! is_null($this->pattern)){
            $out .= " pattern=\"{$this->pattern}\"";
        }

        if(! is_null($this->title)){
            $out .= " title=\"{$this->title}\"";
        }

        $out .= $baseProps;

        $out .= $childProps;

        $out .= "/>";

        return $this->addLabel($out);
    }

}