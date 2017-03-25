<?php
namespace App\Model;

use Core\Mvc\Model\Model;

class RealisateurModel extends Model{

    public function __construct()
    {
        parent::__construct();
    }

    public function getFilmsOf($id){
        $sql = 'SELECT film.id, film.title, film.date, film.rate, film.description, film.id_realisateur FROM film, realisateur WHERE realisateur.id = film.id_realisateur AND realisateur.id = :id';
        return $this->fetchAllInto('film', $sql, ['id' => $id]);
    }

}