<?php
namespace App\Model;

use Core\Database\Database;
use Core\Mvc\Model\Model;

class IndexModel extends Model{

    public function __construct()
    {
        parent::__construct();
    }

    public function authenticate($login,$pwd){
        $sql = "SELECT `id` FROM user WHERE `login` = :login AND `password` = :pwd;";
        $connected = $this->fetch($sql, [
            'login' => $login,
            'pwd' => $pwd
        ]);
        return $connected->id ?? !0;
    }

}