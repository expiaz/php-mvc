<?php

namespace Core\Form\Field\Input;

use Core\Form\Field\AbstractInputField;

class DateInput extends AbstractInputField{

    public function __construct()
    {
        parent::__construct(AbstractInputField::DATE);
    }

    public function build(): string
    {
        return $this->addBaseProps('');
    }

}