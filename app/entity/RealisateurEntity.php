<?php

namespace App\Entity;

use Core\Http\Query;
use Core\Mvc\Entity\Entity;

class RealisateurEntity extends Entity {

    public $id;
    public $name;

    public function __construct()
    {
        parent::__construct();
    }

    public function getFilms(){
        $films = $this->getModel()->getFilmsOf($this->id);
        $out = '<p>Filmographie :</p><ul>';
        foreach ($films as $film) {
            $out .= "<li><a href=\"{$film->getLink()}\">{$film->title} ({$film->date})</a></li>";
        }
        return $out . '</ul>';
    }

    public function getLink(){
        $link = Query::build('realisateur', 'profile', $this->id);
        return $link;
    }

}