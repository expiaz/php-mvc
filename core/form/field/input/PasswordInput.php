<?php

namespace Core\Form\Field\Input;

use Core\Form\Field\AbstractInputField;

class PasswordInput extends AbstractInputField{

    public function __construct()
    {
        parent::__construct(AbstractInputField::PASSWORD);
    }

    public function build(): string
    {
        return $this->addBaseProps('');
    }
}