<?php

namespace Core\Form\Field\Input;

use Core\Form\Field\AbstractInputField;

class SubmitInput extends AbstractInputField {

    public function __construct()
    {
        parent::__construct(AbstractInputField::SUBMIT);
    }

    public function build(): string
    {
        return $this->addBaseProps('');
    }
}