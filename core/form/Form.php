<?php

namespace Core\Form;

use Core\Http\Request;
use Core\Mvc\Model\Model;

class Form{

    private $fields;
    private $class;
    private $id;
    private $enctype;
    private $action;
    private $method;
    private $hasSubmitButton;
    private $entity;
    private $container;
    private $isSubmitted;

    public function __construct(array &$fieldCollection = [], Model $entity = null)
    {
        $this->id = null;
        $this->class = null;
        $this->enctype = null;
        $this->method = 'POST';
        $this->hasSubmitButton = false;
        $this->action = '';
        $this->fields = [];
        $this->entity = $entity;
        $this->isSubmitted = false;

        foreach ($fieldCollection as $field)
            $this->field($field);
    }

    public function field(Field $field){

        if($field->type === 'submit')
            $this->hasSubmitButton = true;

        $this->fields[] = $field;
        return $this;
    }

    public function class($class){
        $this->class = $class;
        return $this;
    }

    public function id($id){
        $this->id = $id;
        return $this;
    }

    public function enctype($enctype){
        switch($enctype){
            case 'file':
                $this->enctype = "multipart/form-data";
                break;
            default:
                $this->enctype = "multipart/form-data";
                break;
        }
        return $this;
    }

    public function action($action){
        $this->action = $action;
        return $this;
    }

    public function method($method){
        $this->method = $method;
        return $this;
    }

    public function build(){
        $out = '<form';
        if($this->method)
            $out .= " method=\"{$this->method}\"";
        else
            $out .= " method=\"POST\"";
        if($this->action)
            $out .= " action=\"{$this->action}\"";
        else
            $out .= " action=\"\"";
        if($this->enctype)
            $out .= " enctype=\"{$this->enctype}\"";
        if($this->class){
            $out .= " class=\"{$this->class}\"";
        }
        if($this->id){
            $out .= " id=\"{$this->id}\"";
        }
        $out .= ">";

        if(!$this->hasSubmitButton)
            $this->field((new Field())->type('submit')->name('submit')->value('submit'));

        $out .= implode('<br/>', array_map(function(Field $f){
            return $f->create();
        }, $this->fields));

        $out .= "</form>";
        return $out;
    }

    private function bindInputEntry($input, $entry){
        switch($input->type){
            case 'submit':
                break;
            default:
                $this->container->{"set" . ucfirst(strtolower($input->name))}($entry);
                break;
        }
    }

    public function handleRequest(Request $request){
        if(!is_null($this->entity)){
            $this->container = $this->entity;
        }
        else{
            $this->container = new DataContainer();
        }

        $method = "get" . ucfirst(strtolower($this->method));

        foreach ($this->fields as $field) {
            $entry = $request->$method($field->name);

            if(!$field->validateEntry($entry)){
                $this->isSubmitted = false;
                return;
            }

            $this->bindInputEntry($field,$entry);
        }

        $this->isSubmitted = true;
    }

    public function isSubmitted(){
        return $this->isSubmitted;
    }

    public function getData(){
        return $this->container;
    }

}