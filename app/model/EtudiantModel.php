<?php

namespace App\Model;

use App\Repository\ConventionRepository;
use Core\App;
use Core\Facade\Contracts\UrlFacade;
use Core\Mvc\Model\Model;

class EtudiantModel extends Model {

    private $prenom;
    private $num_etu;
    private $num_ss;
    private $birthday;
    private $user;

    private $userModel;
    private $projets = [];


    public function getLink(){
        return UrlFacade::create("/etudiant/{$this->id}");
    }

    public function getFullName(){
        return $this->getUserModel()->getNom() . " " . $this->getPrenom();
    }

    public function getEtudeFor($conventionId){
        $binds = [
            'etudiant' => $this->getId(),
            'convention' => $conventionId
        ];
        return $this->getRepository()->getDatabase()->fetch("SELECT * FROM etude WHERE id_etudiant = :etudiant AND id_convention = :convention", $binds);
    }

    /**
     * @return mixed
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param mixed $prenom
     */
    public function setPrenom($prenom)
    {
        parent::setter('prenom', $prenom);
        $this->prenom = $prenom;
    }

    /**
     * @return mixed
     */
    public function getNumEtu()
    {
        return $this->num_etu;
    }

    /**
     * @param mixed $num_etu
     */
    public function setNumEtu($num_etu)
    {
        parent::setter('num_etu', $num_etu);
        $this->num_etu = $num_etu;
    }

    /**
     * @return mixed
     */
    public function getNumSs()
    {
        return $this->num_ss;
    }

    /**
     * @param mixed $num_ss
     */
    public function setNumSs($num_ss)
    {
        parent::setter('num_ss', $num_ss);
        $this->num_ss = $num_ss;
    }

    /**
     * @return mixed
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param mixed $birthday
     */
    public function setBirthday($birthday)
    {
        parent::setter('birthday', $birthday);
        $this->birthday = $birthday;
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
     * @return array
     */
    public function getProjets(): array
    {
        return $this->projets;
    }

    /**
     * @return array
     */
    public function getProjetsModel(): array
    {
        $repo = App::make(ConventionRepository::class);

        return array_map(function($e) use ($repo){ return $repo->getById($e); }, $this->projets);
    }

    /**
     * @param array $projets
     */
    public function setProjets(array $projets)
    {
        $this->projets = $projets;
    }


}