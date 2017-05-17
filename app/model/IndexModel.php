<?php

namespace App\Model;

use Core\Mvc\Model\Model;

class IndexModel extends Model {

    private $title;
    private $date;
    private $rate;
    private $realisateur;
    private $description;
    private $vote;
    private $affiche;

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        parent::setter('title', $title);
        $this->title = $title;
    }


    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        parent::setter('date', $date);
        $this->date = $date;
    }


    /**
     * @return mixed
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param mixed $rate
     */
    public function setRate($rate)
    {
        parent::setter('rate', $rate);
        $this->rate = $rate;
    }


    /**
     * @return mixed
     */
    public function getRealisateur()
    {
        return $this->realisateur;
    }

    /**
     * @param mixed $realisateur
     */
    public function setRealisateur($realisateur)
    {
        parent::setter('realisateur', $realisateur);
        $this->realisateur = $realisateur;
    }


    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        parent::setter('description', $description);
        $this->description = $description;
    }


    /**
     * @return mixed
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * @param mixed $vote
     */
    public function setVote($vote)
    {
        parent::setter('vote', $vote);
        $this->vote = $vote;
    }


    /**
     * @return mixed
     */
    public function getAffiche()
    {
        return $this->affiche;
    }

    /**
     * @param mixed $affiche
     */
    public function setAffiche($affiche)
    {
        $this->setter('affiche', $affiche);
        $this->affiche = $affiche;
    }
}