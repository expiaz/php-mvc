<?php

namespace App\Repository;

use Core\App;
use Core\Config;
use Core\Database\UppletContainer;
use Core\Exception\NoDataFoundException;
use Core\Mvc\Model\Model;
use Core\Mvc\Repository\Repository;
use Core\Utils\DataContainer;

class EtudiantRepository extends Repository {

    public function getAssociedEtudiant($user_id){
        $binds = [
            'user' => $user_id
        ];
        return $this->fetch("SELECT * FROM etudiant WHERE user = :user", $binds);
    }

    public function hydrate(UppletContainer $class): Model
    {
        $etudiant = parent::hydrate($class);

        $etudiant->isReady(false);

        $etudiant->setUserModel(App::make(UserRepository::class)->getById($etudiant->getUser()));
        $sql = sprintf("SELECT id_convention FROM etude WHERE id_etudiant = :id");
        $binds = ['id' => $etudiant->getId()];

        try{
            $projets = $this->database->fetchAll($sql, $binds);
        } catch (NoDataFoundException $e){
            $projets = [];
        }

        $etudiant->setProjets(array_map(function(UppletContainer $etu) {
            return $etu->id_convention;
        }, $projets));

        $etudiant->isReady(true);

        return $etudiant;
    }

}