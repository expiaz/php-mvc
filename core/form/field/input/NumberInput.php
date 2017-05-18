<?php

namespace Core\Form\Field\Input;

use Core\Form\Field\AbstractInputField;

class NumberInput extends AbstractInputField{

    protected $min;
    protected $max;

    public function __construct()
    {
        parent::__construct(AbstractInputField::NUMBER);
        $this->min = -1;
        $this->max = -1;
    }

    /**
     * @return int
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * @param int $min
     */
    public function min($min)
    {
        $this->min = (int) $min;
        return $this;
    }

    /**
     * @return int
     */
    public function getMax(): int
    {
        return $this->max;
    }

    /**
     * @param int $max
     */
    public function max($max)
    {
        $this->max = (int) $max;
        return $this;
    }

    public function build(): string {

        $out = '';

        if($this->max > 0){
            $out .= " max=\"{$this->max}\"";
        }

        if($this->min > 0){
            $out .= " min=\"{$this->min}\"";
        }

        return $this->addBaseProps($out);

    }

    public function validateEntry($entry): bool
    {
        if($this->max > 0 && $entry > $this->max){
            return false;
        }

        if($this->min > 0 && $entry < $this->min){
            return false;
        }

        return parent::validateEntry($entry);
    }


}