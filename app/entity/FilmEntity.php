<?php

namespace App\Entity;

use Core\Http\Query;
use Core\Mvc\Entity\Entity;

class FilmEntity extends Entity{

    public $id;
    public $title;
    public $date;
    public $rate;
    public $realisateur;
    public $description;
    public $_schema = Array ( 0 => Array ( "type" => "hidden", "maxlength" => 11, "name" => "id", "required" => 1, "value" => NULL ), 1 => Array ( "type" => "text", "maxlength" => 100, "name" => "title", "required" => 1, "value" => NULL ), 2 => Array ( "type" => "date", "name" => "date", "required" => 1, "value" => NULL ), 3 => Array ( "type" => "number", "maxlength" => 11, "name" => "rate", "required" => 1, "value" => NULL ), 4 => Array ( "type" => "select", "maxlength" => 11, "name" => "realisateur", "required" => 1, "value" => NULL, "childs" => Array ( 0 => Array ( "name" => "jean lou", "value" => 1 ), 1 => Array ( "name" => "Jean ducas", "value" => 11 ), 2 => Array ( "name" => "feafa", "value" => 12 ), 3 => Array ( "name" => "Martiniquez", "value" => 13 ) ) ), 5 => Array ( "type" => "text", "maxlength" => NULL, "name" => "description", "required" => 1, "value" => NULL ) );

    public function __construct(){
        parent::__construct(func_get_args());
    }

    public function getLink(){
        return Query::build([
            'controller' => 'film',
            'action' => 'profile',
            'param' => $this->id
        ]);
    }

    public function getInfos(){
        $realisateur = $this->getModel('realisateur')->getById($this->realisateur);
        return "<ul><li>titre : {$this->title}</li><li>description : {$this->description}</li><li>realisé par : <a href=\"{$realisateur->getLink()}\">{$realisateur->getName()}</a></li></ul>";
    }

    public function getId(){
        return $this->id;
     }

    public function setId($id){
        parent::setter('id',$id);
        $this->id = $id;
    }

    public function getTitle(){
        return $this->title;
     }

    public function setTitle($title){
        parent::setter('title',$title);
        $this->title = $title;
    }

    public function getDate(){
        return $this->date;
     }

    public function setDate($date){
        parent::setter('date',$date);
        $this->date = $date;
    }

    public function getRate(){
        return $this->rate;
     }

    public function setRate($rate){
        parent::setter('rate',$rate);
        $this->rate = $rate;
    }

    public function getRealisateur(){
        return $this->realisateur;
     }

    public function setRealisateur($realisateur){
        parent::setter('realisateur',$realisateur);
        $this->realisateur = $realisateur;
    }

    public function getDescription(){
        return $this->description;
     }

    public function setDescription($description){
        parent::setter('description',$description);
        $this->description = $description;
    }

}