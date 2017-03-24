<?php
namespace Core\Mvc\Entity;

abstract class Entity{

    public $_modified = [];

    abstract function setter($property,$value);

}