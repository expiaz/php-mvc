<?php

namespace App\Model;

use Core\Mvc\Model\Model;

class RealisateurModel extends Model{

    public function __construct(){
        parent::__construct();
    }

    public function getFilms($realisateur){
        return $this->fetchAllInto('film', 'SELECT * FROM film WHERE realisateur = ?', [$realisateur]);
    }

}