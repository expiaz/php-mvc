<?php

namespace App\Model;

use Core\Mvc\Model\Model;

class UserModel extends Model {

    public function __construct($table = 'user')
    {
        parent::__construct($table);
    }

}