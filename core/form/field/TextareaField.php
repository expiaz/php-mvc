<?php

namespace Core\Form\Field;

class TextareaField extends AbstractField{

    protected $maxlength;
    protected $rows;
    protected $cols;

    public function __construct()
    {
        parent::__construct(AbstractField::TEXTAREA);
        $this->maxlength = -1;
        $this->rows = -1;
        $this->cols = -1;
        $this->value = '';
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
    }


    /**
     * @return mixed
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @param mixed $rows
     */
    public function rows($rows)
    {
        $this->rows = (int) $rows;
    }


    /**
     * @return mixed
     */
    public function getCols()
    {
        return $this->cols;
    }

    /**
     * @param mixed $cols
     */
    public function cols($cols)
    {
        $this->cols = (int) $cols;
    }


    public function build(): string
    {
        $baseProps = $this->buildCommonProps();

        $out = "<textarea ";

        if($this->maxlength > 0){
            $out .= " maxlength=\"{$this->maxlength}\"";
        }

        if($this->rows > 0){
            $out .= " rows=\"{$this->rows}\"";
        }

        if($this->cols > 0){
            $out .= " cols=\"{$this->cols}\"";
        }

        $out .= $baseProps;

        $out .= ">{$this->value}</textarea>";

        return $this->addLabel($out);
    }

    public function validateEntry($entry): bool
    {

        if($this->maxlength > 0 && strlen($entry) > $this->maxlength){
            return false;
        }

        return parent::validateEntry($entry);
    }



}