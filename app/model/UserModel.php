<?php

namespace App\Model;

use Core\Config;
use Core\Mvc\Model\Model;

class UserModel extends Model {

    private $name;
    private $login;
    private $password;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        parent::setter('name', $name);
        $this->name = $name;
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


}