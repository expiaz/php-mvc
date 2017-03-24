<?php

namespace App\Entity;

use Core\Mvc\Entity\Entity;

class UserEntity extends Entity {

    private $id;
    private $login;
    private $password;
    private $pseudo;
    private $mail;

    public function setter($property,$value){
        if (property_exists($this, $property)) {
            if($this->$property !== $value){
                $this->$property = $value;
                $this->_modified[$property] = $value;
            }
        }
    }

    public function getInfos(){
        return $this->id . ' : ' . $this->pseudo;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->setter('id',$id);
        $this->id = $id;
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
        $this->setter('login', $login);
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
        $this->setter('password', $password);
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * @param mixed $pseudo
     */
    public function setPseudo($pseudo)
    {
        $this->setter('pseudo', $pseudo);
        $this->pseudo = $pseudo;
    }

    /**
     * @return mixed
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param mixed $mail
     */
    public function setMail($mail)
    {
        $this->setter('mail', $mail);
        $this->mail = $mail;
    }


}