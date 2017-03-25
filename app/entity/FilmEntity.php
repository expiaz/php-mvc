<?php

namespace App\Entity;

use Core\Http\Query;
use Core\Mvc\Entity\Entity;

class FilmEntity extends Entity {

    public $id;
    public $title;
    public $date;
    public $rate;
    public $description;
    public $realisateur;

    public function __construct()
    {
        parent::__construct();
    }

    public function getInfos(){
        $realisateur = $this->getModel()->getRealisateurOf($this->id);
        return "
            <ul>
                <li>Titre : <a href=\"{$this->getLink()}\">{$this->title}</a></li>
                <li>Realisateur : <a href=\"{$realisateur->getLink()}\">{$realisateur->name}</a></li>
                <li>Date de sortie : {$this->date}</li>
                <li>Description : {$this->description}</li>
                <li>Critiques : {$this->rate}/10</li>
            </ul>
        ";
    }

    public function getLink(){
        $link = Query::build('film', 'profile', $this->id);
        return $link;
    }

}