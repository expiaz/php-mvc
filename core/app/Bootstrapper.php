<?php
namespace Core\App;

class Bootstrapper{

    public function __construct()
    {
        $this->loadStatics();
        //$this->loadMvc();
        //$this->initialize();
    }

    public function loadStatics(){

        //require_once CORE_SHARED . 'webConstants.php';

        require_once APP . 'route.php';
    }

    public function loadMvc(){
        require_once CORE_MVC . 'view' . DS . 'View.php';
        require_once CORE_MVC . 'repository' . DS . 'Repository.php';
        require_once CORE_MVC . 'model' . DS . 'Model.php';
        require_once CORE_MVC . 'controller' . DS . 'Controller.php';
    }

    public function initialize(){
        //Cache::init();
        //database\Database::connect();
        //http\Session::activate();
    }

}