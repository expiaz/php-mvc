<?php

namespace Core\Form;

use Core\Form\Field\AbstractField;
use Core\Form\Field\AbstractInputField;
use Core\Form\Field\Input\SubmitInput;
use Core\Http\Request;
use Core\Mvc\Model\Model;
use Core\Utils\DataContainer;

class Form{

    private $fields;
    private $class;
    private $id;
    private $enctype;
    private $action;
    private $method;
    private $hasSubmitButton;
    private $isSubmitted;

    private $model;

    public function __construct(array &$fieldCollection = [], Model $model = null)
    {
        $this->id = null;
        $this->class = null;
        $this->enctype = null;
        $this->method = 'POST';
        $this->hasSubmitButton = false;
        $this->action = '';
        $this->fields = [];
        $this->model = $model ? $model : new DataContainer();
        $this->isSubmitted = false;

        foreach ($fieldCollection as $field){
            $this->field($field);
        }
    }

    public function field(AbstractField $field): Form
    {
        if($field->getFieldType() === AbstractField::INPUT){
            if($field->getType() === AbstractInputField::SUBMIT){
                $this->hasSubmitButton = true;
            } else if($field->getType() === AbstractInputField::FILE){
                $this->enctype(AbstractInputField::FILE);
            }
        }

        $this->fields[] = $field;
        return $this;
    }

    public function class(string $class): Form
    {
        $this->class = $class;
        return $this;
    }

    public function getClass(){
        return $this->class;
    }


    public function id(string $id): Form
    {
        $this->id = $id;
        return $this;
    }

    public function getId(){
        return $this->id;
    }


    public function enctype(string $enctype): Form
    {
        switch($enctype){
            case 'file':
                $this->enctype = 'multipart/form-data';
                break;
            default:
                $this->enctype = 'multipart/form-data';
                break;
        }
        return $this;
    }

    public function getEnctype(){
        return $this->enctype;
    }


    public function action(string $action): Form
    {
        $this->action = $action;
        return $this;
    }

    public function getAction(){
        return $this->action;
    }


    public function method(string $method): Form
    {
        $this->method = $method;
        return $this;
    }

    public function getMethod(){
        return $this->method;
    }


    public function build(): string
    {
        $out = '<form';

        if(! is_null($this->method)){
            $out .= " method=\"{$this->method}\"";
        } else {
            $out .= " method=\"POST\"";
        }

        if(! is_null($this->action)){
            $out .= " action=\"{$this->action}\"";
        } else {
            $action = \Url::create(\Request::getUrl());
            $out .= " action=\"{$action}\"";
        }

        if(! is_null($this->enctype)){
            $out .= " enctype=\"{$this->enctype}\"";
        }

        if(! is_null($this->class)){
            $out .= " class=\"{$this->class}\"";
        }

        if(! is_null($this->id)){
            $out .= " id=\"{$this->id}\"";
        }

        $out .= ">";

        if(! $this->hasSubmitButton){
            $this->field((new SubmitInput())
                ->name('submit')
                ->value('submit'));
        }

        $out .= implode('<br/>', array_map(function(AbstractField $f){
            return $f->build();
        }, $this->fields) );

        $out .= "</form>";

        return $out;
    }


    public function __toString()
    {
        return $this->build();
    }

    private function hydrateModel(AbstractField $input)
    {
        if($input->getFieldType() === AbstractField::INPUT && $input->getType() === AbstractInputField::SUBMIT){
            return;
        }

        $fieldName = ucfirst(strtolower($input->getName()));
        $actualValue = $this->model->{"get{$fieldName}"}();
        $fieldEntry = $input->getValue();

        if($actualValue === $fieldEntry)
            return;

        $this->model->{"set{$fieldName}"}($fieldEntry);
    }

    public function handleRequest(Request $request)
    {
        $method = strtolower($this->method);

        if(strtolower($request->getMethod()) !== $method){
            $this->isSubmitted = false;
            return;
        }

        switch ($method){
            case 'get':
                $payload = $request->getGet();
                break;
            case 'post':
                $payload = $request->getPost();
                break;
            default:
                throw new \Exception("[Form::handleRequest] {$method} is not a form method");
        }

        foreach ($this->fields as $field) {
            $entry = isset($payload[$field->getName()]) && strlen($payload[$field->getName()]) > 0 ? $payload[$field->getName()] : null;

            if(! $field->validateEntry($entry)){
                $this->isSubmitted = false;
                return false;
            }
        }

        foreach ($this->fields as $field){
            $entry = isset($payload[$field->getName()]) && strlen($payload[$field->getName()]) > 0 ? $payload[$field->getName()] : null;

            $field->bindEntry($entry);
            $this->hydrateModel($field);
        }

        $this->isSubmitted = true;

        return true;

    }

    public function isSubmitted(): bool
    {
        return $this->isSubmitted;
    }

    public function getData()
    {
        return $this->model;
    }

}