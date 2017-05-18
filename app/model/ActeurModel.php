<?php

namespace App\Model;

use Core\Mvc\Model\Model;

class ActeurModel extends Model {

    private $name;
    private $films = [];

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

    public function addFilm(FilmModel $film){
        $this->films[] = $film;
    }

}