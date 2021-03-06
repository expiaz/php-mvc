<?php

namespace App\Repository;

use Core\Config;
use Core\Mvc\Model\Model;
use Core\Mvc\Repository\Repository;
use Core\Utils\DataContainer;

class UserRepository extends Repository {

    public function hashPassword($password){
        return password_hash($password, PASSWORD_BCRYPT, container(Config::class)['password']);
    }

    public function insert($o)
    {
        $o->setPassword($this->hashPassword($o->getPassword()));
        return parent::insert($o); // TODO: Change the autogenerated stub
    }

    public function auth($login, $password){
        return $this->getByFields([
            'login' => $login,
            'password' => $this->hashPassword($password)
        ])[0];
    }

}