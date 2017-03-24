<?php

namespace App\Entity;

use Core\Http\Query;
use Core\Mvc\Entity\Entity;

class UserEntity extends Entity {

    public $id;
    public $login;
    public $password;
    public $pseudo;
    public $mail;

    public function getInfos(){
        return "
            <ul>
                <li>id : {$this->id}</li>
                <li>login : {$this->login}</li>
                <li>pseudo : {$this->pseudo}</li>
                <li>mail : {$this->mail}</li>
            </ul>
        ";
    }

    public function getProfileLink(){
        $link = Query::build(Query::getController(), 'profile', $this->id);
        return $link;
    }

}