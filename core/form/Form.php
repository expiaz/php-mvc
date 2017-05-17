<?php

namespace Core\Form;

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

    public function field(Field $field): Form
    {
        if($field->type === 'submit')
            $this->hasSubmitButton = true;

        $this->fields[] = $field;
        return $this;
    }

    public function class(string $class): Form
    {
        $this->class = $class;
        return $this;
    }

    public function id(string $id): Form
    {
        $this->id = $id;
        return $this;
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

    public function action(string $action): Form
    {
        $this->action = $action;
        return $this;
    }

    public function method(string $method): Form
    {
        $this->method = $method;
        return $this;
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
            $out .= " action=\"\"";
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

        if(!$this->hasSubmitButton){
            $this->field((new Field())->type('submit')->name('submit')->value('submit'));
        }

        $out .= implode('<br/>', array_map(function(Field $f){
            return $f->create();
        }, $this->fields) );

        $out .= "</form>";
        return $out;
    }


    public function __toString()
    {
        return $this->build();
    }

    private function bindInputEntry(Field $input, $entry)
    {
        switch($input->getType()){
            case 'submit':
                break;
            default:
                $fieldName = ucfirst(strtolower($input->getName()));
                $actualValue = $this->model->{"get{$fieldName}"}();
                if($actualValue === $entry)
                    return;

                $this->model->{"set{$fieldName}"}($entry);
                $input->value($entry);
                break;
        }
    }

    public function handleRequest(Request $request)
    {
        $method = strtolower($this->method);
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
            $entry = $payload[$field->getName()];

            if(! $field->validateEntry($entry)){
                $this->isSubmitted = false;
                return;
            }
        }

        foreach ($this->fields as $field){
            $entry = $payload[$field->getName()];
            $this->bindInputEntry($field,$entry);
        }

        $this->isSubmitted = true;

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