<?php

namespace App\Model;

use Core\Mvc\Model\Model;

class UserModel extends Model {

    private $nom;
    private $login;
    private $password;
    private $admin;

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom)
    {
        parent::setter('nom', $nom);
        $this->nom = $nom;
    }


    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     */
    public function setLogin($login)
    {
        parent::setter('login', $login);
        $this->login = $login;
    }


    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        parent::setter('password', $password);
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function isAdmin()
    {
        return $this->admin;
    }

    /**
     * @param mixed $admin
     */
    public function setAdmin($admin)
    {
        parent::setter('admin', $admin);
        $this->admin = (bool) $admin;
    }


}