<?php

namespace Core\Form\Field\Input;

use Core\Form\Field\Input;
use Core\Form\Field\AbstractInputField;

class ResetInput extends AbstractInputField {

    public function __construct()
    {
        parent::__construct(AbstractInputField::RESET);
    }

    public function build(): string
    {
        return $this->addBaseProps('');
    }
}