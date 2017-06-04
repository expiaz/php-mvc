<?php

namespace Core\Form\Field\Input;

use Core\Form\Field\AbstractInputField;

class CheckBoxInput extends AbstractInputField{

    protected $checked;

    public function __construct()
    {
        parent::__construct(AbstractInputField::CHECKBOX);
        $this->checked = false;
    }

    public function checked($check = true){
        $this->checked = $check;
    }

    public function isChecked(){
        return $this->checked;
    }

    public function build(): string
    {
        return $this->addBaseProps($this->checked ? ' checked' : '');
    }

}