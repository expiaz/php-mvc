<?php

namespace Core\Form\Field\Input;

use Core\Form\Field\AbstractInputField;

class TextInput extends AbstractInputField{

    protected $maxlength;

    public function __construct()
    {
        parent::__construct(AbstractInputField::TEXT);
        $this->maxlength = -1;
    }

    /**
     * @return mixed
     */
    public function getMaxlength()
    {
        return $this->maxlength;
    }

    /**
     * @param mixed $maxlength
     */
    public function maxlength($maxlength)
    {
        $this->maxlength = (int) $maxlength;
        return $this;
    }


    public function build(): string {
        $out = '';
        if($this->maxlength > 0){
            $out .= " maxlength=\"{$this->maxlength}\"";
        }
        return $this->addBaseProps($out);
    }



}