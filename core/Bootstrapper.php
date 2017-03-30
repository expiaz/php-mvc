<?php
namespace Core;

class Bootstrapper{

    public function __construct()
    {
        $this->loadStatics();
        $this->loadMvc();
        $this->initialize();
    }

    public function loadStatics(){
        require_once CORE . 'Autoload.php';
        require_once CORE . 'Helper.php';
        require_once CORE . 'Config.php';
        require_once CORE . 'Cache.php';
        require_once CORE_HTTP . 'Cookie.php';
        require_once CORE_HTTP . 'Session.php';
        require_once CORE_HTTP . 'Query.php';
        require_once CORE_HTTP . 'Router.php';
        require_once CORE_DATABASE . 'Database.php';
        require_once CORE_DATABASE . 'ORM.php';
        require_once CORE_FORM . 'FormBuilder.php';
        require_once APP . 'route.php';
    }

    public function loadMvc(){
        require_once CORE_MVC .'view' . DS . 'View.php';
        require_once CORE_MVC .'entity' . DS . 'Entity.php';
        require_once CORE_MVC .'model' . DS . 'Model.php';
        require_once CORE_MVC .'controller' . DS . 'Controller.php';
    }

    public function initialize(){
        Autoload::register();
        Cache::init();
        database\Database::connect();
        http\Session::activate();
    }

}