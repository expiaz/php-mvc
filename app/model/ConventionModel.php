<?php

namespace App\Model;

use App\Repository\EtudiantRepository;
use Core\App;
use Core\Facade\Contracts\UrlFacade;
use Core\Mvc\Model\Model;

class ConventionModel extends Model {

    private $titre;
    private $date_creation;
    private $duree;
    private $date_fin;
    private $date_debut;
    private $prix_j;
    private $entreprise;

    private $entrepriseModel;
    private $etudiants = [];

    public function getLink(){
        return UrlFacade::create("/projets/{$this->id}");
    }

    /**
     * @return mixed
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * @param mixed $titre
     */
    public function setTitre($titre)
    {
        parent::setter('titre', $titre);
        $this->titre = $titre;
    }

    /**
     * @return mixed
     */
    public function getDateCreation()
    {
        return $this->date_creation;
    }

    /**
     * @param mixed $date_creation
     */
    public function setDateCreation($date_creation)
    {
        parent::setter('date_creation', $date_creation);
        $this->date_creation = $date_creation;
    }

    /**
     * @return mixed
     */
    public function getDuree()
    {
        return $this->duree;
    }

    /**
     * @param mixed $duree
     */
    public function setDuree($duree)
    {
        parent::setter('duree', $duree);
        $this->duree = $duree;
    }

    /**
     * @return mixed
     */
    public function getDateFin()
    {
        return $this->date_fin;
    }

    /**
     * @param mixed $date_fin
     */
    public function setDateFin($date_fin)
    {
        parent::setter('date_fin', $date_fin);
        $this->date_fin = $date_fin;
    }

    /**
     * @return mixed
     */
    public function getDateDebut()
    {
        return $this->date_debut;
    }

    /**
     * @param mixed $date_debut
     */
    public function setDateDebut($date_debut)
    {
        parent::setter('date_debut', $date_debut);
        $this->date_debut = $date_debut;
    }

    /**
     * @return mixed
     */
    public function getPrixJ()
    {
        return $this->prix_j;
    }

    /**
     * @param mixed $prix_j
     */
    public function setPrixJ($prix_j)
    {
        parent::setter('prix_j', $prix_j);
        $this->prix_j = $prix_j;
    }

    /**
     * @return mixed
     */
    public function getEntreprise()
    {
        return $this->entreprise;
    }

    /**
     * @param mixed $entreprise
     */
    public function setEntreprise($entreprise)
    {
        parent::setter('entreprise', $entreprise);
        $this->entreprise = $entreprise;
    }

    /**
     * @return mixed
     */
    public function getEntrepriseModel()
    {
        return $this->entrepriseModel;
    }

    /**
     * @param mixed $entrepriseModel
     */
    public function setEntrepriseModel($entrepriseModel)
    {
        $this->entrepriseModel = $entrepriseModel;
    }

    /**
     * @return array
     */
    public function getEtudiants(): array
    {
        return $this->etudiants;
    }

    /**
     * @return array
     */
    public function getEtudiantsModel(): array
    {
        $repo = App::make(EtudiantRepository::class);

        return array_map(function($etu) use ($repo){
            return $repo->getById($etu);
        }, $this->etudiants);
    }

    /**
     * @param array $etudiants
     */
    public function setEtudiants(array $etudiants)
    {
        $this->etudiants = $etudiants;
    }

}