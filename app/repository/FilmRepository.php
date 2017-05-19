<?php

namespace App\Repository;

use App\Model\FilmModel;
use App\Model\RealisateurModel;
use Core\App;
use Core\Exception\NoDataFoundException;
use Core\Exception\SqlAlterException;
use Core\Mvc\Model\Model;
use Core\Mvc\Repository\Repository;
use Core\Utils\DataContainer;

class FilmRepository extends Repository {

    public function getRealisateur($realisateurId): RealisateurModel
    {
        $realisateurRepo = App::make(RealisateurRepository::class);
        return $realisateurRepo->getById($realisateurId);
    }

    public function getActeurs(array $acteursId){
        $acteurRepo = App::make(ActeurRepository::class);
        $acteurs = [];
        foreach ($acteursId as $acteurId){
            $acteurs[] = $acteurRepo->getById($acteurId);
        }

        return $acteurs;
    }

    protected function hydrate(DataContainer $class): Model
    {
        $model = parent::hydrate($class);
        $model->isReady(false);

        $sql = sprintf(
            "SELECT %s AS id FROM %s, %s, %s WHERE %s = %s AND %s = %s AND %s = %s",
            'acteur.id',
            'film',
            'acteur',
            'film_acteur',
            'film.id',
            'film_acteur.film_id',
            'acteur.id',
            'film_acteur.acteur_id',
            'film.id',
            ':id'
        );

        $v = ['id' => $model->getId()];
        $acteurs = $this->database->raw($sql,$v);

        $acteursId = array_map(function ($e){
            return $e->id;
        }, $acteurs);

        $model->setActeur($acteursId);
        $model->isReady();

        return $model;
    }

    public function insert(Model $o)
    {

        parent::insert($o);

        //let's insert acteurs
        $acteurs = $o->getActeur();

        if(is_null($acteurs)){
            $o->setActeur([]);
            return parent::insert($o);
        }

        if(! is_array($acteurs)){
            $o->setActeur([]);
            return parent::insert($o);
        }

        if(count($acteurs) === 0){
            return parent::insert($o);
        }

        $pdo = $this->database->getConnection();

        $pdo->beginTransaction();
        $p = [];
        foreach ($acteurs as $acteurId){
            $p['film'] = $o->getId();
            $p['acteur'] = $acteurId;
            if($this->database->execute("INSERT INTO film_acteur VALUES (:film, :acteur);",$p) === false){
                $pdo->rollBack();
                throw new SqlAlterException("[FilmRepository::update] error : INSERT INTO film_acteur VALUES ({$p['film']}, {$p['acteur']}");
            }
        }
        $pdo->commit();

        return true;

    }

    public function update(Model $o){

        $sql = "SELECT acteur.id AS id FROM film, acteur, film_acteur WHERE film_acteur.acteur_id = acteur.id AND film_acteur.film_id = film.id AND film.id = :id";

        $p = ['id' => $o->getId()];
        $bddRelations = array_map(function($e){
            return $e->id;
        }, $this->database->raw($sql, $p));

        $nowRelations = $o->getActeur();

        $insert = array_diff($nowRelations, $bddRelations);
        $delete = array_diff($bddRelations, $nowRelations);

        $pdo = $this->database->getConnection();

        if(count($insert)){

            $pdo->beginTransaction();
            $p = [];
            foreach ($insert as $acteurId){
                $p['film'] = $o->getId();
                $p['acteur'] = $acteurId;
                if($this->database->execute("INSERT INTO film_acteur VALUES (:film, :acteur);",$p) === false){
                    $pdo->rollBack();
                    throw new SqlAlterException("[FilmRepository::update] error : INSERT INTO film_acteur VALUES ({$p['film']}, {$p['acteur']}");
                }
            }
            $pdo->commit();

        }

        if(count($delete)){

            $pdo->beginTransaction();
            $p = [];
            foreach ($delete as $acteurId){
                $p['film'] = $o->getId();
                $p['acteur'] = $acteurId;
                if($this->database->execute("DELETE FROM film_acteur WHERE film_id = :film AND acteur_id = :acteur;",$p) === false){
                    $pdo->rollBack();
                    throw new SqlAlterException("[FilmRepository::update] error : DELETE FROM film_acteur WHERE id_film = {$p['film']} AND id_acteur = {$p['acteur']};");
                }
            }
            $pdo->commit();

        }

        return parent::update($o);


    }

}