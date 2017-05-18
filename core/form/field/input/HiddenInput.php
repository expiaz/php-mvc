<?php

namespace Core\Form\Field\Input;

use Core\Form\Field\AbstractInputField;

class HiddenInput extends AbstractInputField{

    public function __construct()
    {
        parent::__construct(AbstractInputField::HIDDEN);
    }

    public function build(): string
    {
        return $this->addBaseProps('');
    }

}