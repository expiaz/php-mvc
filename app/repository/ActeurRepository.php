<?php

namespace App\Repository;

use Core\App;
use Core\Mvc\Repository\Repository;
use Core\Utils\DataContainer;
use Core\Mvc\Model\Model;

class ActeurRepository extends Repository {

    protected function hydrate(DataContainer $class): Model
    {
        $model = parent::hydrate($class);
        $model->isReady(false);

        $sql = sprintf(
            "SELECT %s AS id FROM %s, %s, %s WHERE %s = %s AND %s = %s AND %s = %s",
            'film.id',
            'film',
            'acteur',
            'film_acteur',
            'film.id',
            'film_acteur.film_id',
            'acteur.id',
            'film_acteur.acteur_id',
            'acteur.id',
            ':id'
        );

        $v = ['id' => $model->getId()];
        $films = $this->database->raw($sql,$v);

        $filmsId = array_map(function ($e){
            return $e->id;
        }, $films);

        $model->setFilms($filmsId);
        $model->isReady();

        return $model;
    }

    public function &getFilms(array $films){
        $filmRepo = App::make(FilmRepository::class);

        $filmsModel = [];
        foreach ($films as $film){
            $filmsModel[] = $filmRepo->getById($film);
        }

        return $filmsModel;
    }

}