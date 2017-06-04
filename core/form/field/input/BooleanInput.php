<?php

namespace Core\Form\Field\Input;

use Core\Form\Field\AbstractInputField;

class BooleanInput extends AbstractInputField{

    public function __construct()
    {
        parent::__construct(AbstractInputField::BOOLEAN);
    }

    public function build(): string {

        $baseProps = $this->buildCommonProps();

        $name = ! is_null($this->name) ? $this->name : $this->type;

        $yes = "<input type=\"radio\" name=\"{$name}\" value=\"1\" {$baseProps}/>Yes";
        $no = "<input type=\"radio\" name=\"{$name}\" value=\"0\" {$baseProps} selected/>No";

        return $this->addLabel($yes.$no);
    }

}