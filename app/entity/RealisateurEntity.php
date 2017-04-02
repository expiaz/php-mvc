<?php

namespace App\Entity;

use Core\Http\Query;
use Core\Mvc\Entity\Entity;

class RealisateurEntity extends Entity{

    public $id;
    public $name;
    public $_schema = Array ( 0 => Array ( "type" => "hidden", "maxlength" => 11, "name" => "id", "required" => 1, "value" => NULL ), 1 => Array ( "type" => "text", "maxlength" => 50, "name" => "name", "required" => 1, "value" => NULL ) );

    public function __construct(){
        parent::__construct(func_get_args());
    }

    public function getInfos(){
        $films = $this->getModel()->getFilms($this->id);
        $out = '<ul>';
        foreach ($films as $film){
            $out .= "<li><a href=\"{$film->getLink()}\">{$film->getTitle()}</a></li>";
        }
        $out .= '</ul>';
        return $out;
    }

    public function getLink(){
        return Query::build([
            'controller' => 'realisateur',
            'action' => 'profile',
            'param' => $this->id
        ]);
    }

    public function getId(){
        return $this->id;
     }

    public function setId($id){
        parent::setter('id',$id);
        $this->id = $id;
    }

    public function getName(){
        return $this->name;
     }

    public function setName($name){
        parent::setter('name',$name);
        $this->name = $name;
    }

}