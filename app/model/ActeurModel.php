<?php

namespace App\Model;

use Core\Facade\Contracts\UrlFacade;
use Core\Mvc\Model\Model;

class ActeurModel extends Model {

    private $name;
    private $films = [];

    public function getLink(){
        return UrlFacade::create("/acteur/{$this->getId()}");
    }

    public function getEditLink(){
        return UrlFacade::create("/acteur/edit/{$this->getId()}");
    }

    public function getFilmsModel(){
        return $this->getRepository()->getFilms($this->films);
    }

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
     * @return array
     */
    public function getFilms(): array
    {

        return $this->films;
    }

    /**
     * @param array $films
     */
    public function setFilms(array $films)
    {
        $this->films = $films;
    }

    public function addFilmModel(FilmModel $film){
        $this->films[] = $film;
    }

}