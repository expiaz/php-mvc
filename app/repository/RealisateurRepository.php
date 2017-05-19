<?php

namespace App\Repository;

use Core\Mvc\Repository\Repository;
use Core\Mvc\Model\Model;
use Core\Utils\DataContainer;

class RealisateurRepository extends Repository
{

    protected function hydrate(DataContainer $class): Model
    {
        $model = parent::hydrate($class);
        $model->isReady(false);

        $sql = "SELECT film.id AS id FROM film, realisateur WHERE film.realisateur = realisateur.id AND realisateur.id = :id";

        $v = ['id' => $model->getId()];
        $films = $this->database->raw($sql, $v);

        $filmsId = array_map(function ($e) {
            return $e->id;
        }, $films);

        $model->setFilms($filmsId);
        $model->isReady();

        return $model;
    }

}

