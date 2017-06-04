<?php

namespace App\Repository;

use Core\App;
use Core\Database\UppletContainer;
use Core\Exception\NoDataFoundException;
use Core\Exception\SqlAlterException;
use Core\Mvc\Model\Model;
use Core\Mvc\Repository\Repository;
use Core\Utils\DataContainer;

class ConventionRepository extends Repository {

    public function hydrate(UppletContainer $class): Model
    {
        $convention = parent::hydrate($class);

        $convention->isReady(false);

        $convention->setEntrepriseModel(App::make(EntrepriseRepository::class)->getById($convention->getEntreprise()));

        $sql = sprintf("SELECT id_etudiant FROM etude WHERE id_convention = :id");
        $binds = ['id' => $convention->getId()];
        try{
            $etudiants = $this->database->fetchAll($sql, $binds);
        } catch (NoDataFoundException $e){
            $etudiants = [];
        }

        $convention->setEtudiants(array_map(function(UppletContainer $etu) {
            return $etu->id_etudiant;
        }, $etudiants));

        $convention->isReady(true);

        //var_dump($convention->getModifications());

        return $convention;
    }

    public function insert($o)
    {
        $model = $o;

        if($o instanceof DataContainer) {
            $model = $this->getModel();
            foreach ($o->keys() as $key){
                if(property_exists($model, $key)){
                    $setter = "set{$this->toCamelCase($key)}";
                    $model->{ $setter }( $o[$key] );
                }
            }
        }

        $model->setDateCreation(date("Y-m-d"));
        $model->setDateDebut(date("Y-m-d"));

        //var_dump($model);
        //die();

        $projetId = parent::insert($model);

        $pdo = $this->database->getConnection();

        $pdo->beginTransaction();

        $sql = "INSERT INTO etude VALUES (:etudiant, :projet, :chef, :remuneration, 0)";
        $query = $pdo->prepare($sql);
        foreach ($model->getEtudiants() as $i => $etudiant){
            $binds = [
                'etudiant' => $etudiant,
                'projet' => $projetId,
                'chef' => $o['chef'] == ($i+1) ? 1 : 0,
                'remuneration' => $o['remunerations'][$i]
            ];
            if( $query->execute($binds) === false){
                $pdo->rollBack();
                throw new SqlAlterException("ConventionRepository::insert failed to insert etude with request $sql and bindings " . print_r($binds, true));
            }
        }

        $pdo->commit();
    }

}