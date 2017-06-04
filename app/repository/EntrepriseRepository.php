<?php

namespace App\Repository;

use Core\App;
use Core\Config;
use Core\Database\UppletContainer;
use Core\Exception\NoDataFoundException;
use Core\Mvc\Model\Model;
use Core\Mvc\Repository\Repository;
use Core\Utils\DataContainer;

class EntrepriseRepository extends Repository {

    public function getAssociedEntreprise($user_id){
        $binds = [
            'user' => $user_id
        ];
        return $this->fetch("SELECT * FROM entreprise WHERE user = :user", $binds);
    }

    public function hydrate(UppletContainer $class): Model
    {
        $entreprise = parent::hydrate($class);

        $entreprise->isReady(false);

        $entreprise->setUserModel(App::make(UserRepository::class)->getById($entreprise->getUser()));

        $sql = sprintf("SELECT id FROM convention WHERE entreprise = :id");
        $binds = ['id' => $entreprise->getId()];

        try{
            $conventions = $this->database->fetchAll($sql, $binds);
        } catch (NoDataFoundException $e){
            $conventions = [];
        }
        $entreprise->isReady(true);

        $entreprise->setConventions(array_map(function($etu) {
            return $etu->id;
        }, $conventions));

        return $entreprise;
    }

}