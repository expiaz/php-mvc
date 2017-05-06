<?php

namespace App\Model;

use Core\Mvc\Model\Model;

class IndexModel extends Model {

    private $affiche;
    private $mailing;

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

    /**
     * @return mixed
     */
    public function getMailing()
    {
        return $this->mailing;
    }

    /**
     * @param mixed $mailing
     */
    public function setMailing($mailing)
    {
        $this->setter('mailing', $mailing);
        $this->mailing = $mailing;
    }


}