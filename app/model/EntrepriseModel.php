<?php

namespace App\Model;

use App\Repository\ConventionRepository;
use Core\App;
use Core\Mvc\Model\Model;

class EntrepriseModel extends Model {

    private $adresse;
    private $siret;
    private $tel;
    private $raison;
    private $user;

    private $userModel;
    private $conventions = [];


    public function getLink(){
        return UrlFacade::create("/entreprise/{$this->id}");
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        parent::setter('user', $user);
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUserModel(): UserModel
    {
        return $this->userModel;
    }

    /**
     * @param mixed $userModel
     */
    public function setUserModel(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    /**
     * @return mixed
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * @param mixed $adresse
     */
    public function setAdresse($adresse)
    {
        parent::setter('adresse', $adresse);
        $this->adresse = $adresse;
    }

    /**
     * @return mixed
     */
    public function getSiret()
    {
        return $this->siret;
    }

    /**
     * @param mixed $siret
     */
    public function setSiret($siret)
    {
        parent::setter('siret', $siret);
        $this->siret = $siret;
    }

    /**
     * @return mixed
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * @param mixed $tel
     */
    public function setTel($tel)
    {
        parent::setter('tel', $tel);
        $this->tel = $tel;
    }

    /**
     * @return mixed
     */
    public function getRaison()
    {
        return $this->raison;
    }

    /**
     * @param mixed $raison
     */
    public function setRaison($raison)
    {
        parent::setter('raison', $raison);
        $this->raison = $raison;
    }

    /**
     * @return array
     */
    public function getConventions(): array
    {
        $repo = App::make(ConventionRepository::class);

        return array_map(function($e) use ($repo) { return $repo->getById($e); }, $this->conventions);
    }

    /**
     * @param array $conventions
     */
    public function setConventions(array $conventions)
    {
        $this->conventions = $conventions;
    }


}