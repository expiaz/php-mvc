<?php

namespace Core\Facade\Contracts;

use Core\Facade\Facade;
use Core\Form\Form;
use Core\Form\FormBuilder;
use Core\Mvc\Model\Model;

class FormFacade extends Facade {


    static function getFacadeAccessor(): string
    {
        return 'Form';
    }

    static function getFacadedClass(): string
    {
        return Form::class;
    }

    static function create(Model $model){
        return static::getContainer(FormBuilder::class)->build($model);
    }
}