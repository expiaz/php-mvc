<?php
namespace App\Model;

use Core\Mvc\Model\Model;

class FilmModel extends Model{

    public function __construct()
    {
        parent::__construct();
    }

    public function getRealisateurOf($id){
        $sql = 'SELECT realisateur.id, realisateur.name FROM film, realisateur WHERE realisateur.id = film.id_realisateur AND realisateur.id = :id';
        return $this->fetchInto('realisateur', $sql, ['id' => $id]);
    }

}