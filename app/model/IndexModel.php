<?php

namespace App\Model;

use Core\Mvc\Model\Model;

class IndexModel extends Model {

    protected $id;
    private $name;

    public function __construct(){
        parent::__construct();
    }

    public function getId(){
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        parent::setter('id', $id);
        $this->id = $id;
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



}