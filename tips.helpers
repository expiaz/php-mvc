#in a .htaccess file
#to activate the redirections on the frontController

RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-l

RewriteRule ^(.+)$ index.php?query=$1 [QSA,L]

<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', __DIR__ . DS);
define('WEBHOST', $_SERVER['HTTP_HOST']);
define('WEBROOT', 'http://' . WEBHOST);

class FrontController{

    private $controller;
    private $action;
    private $parameters;

    function __construct(string $url)
    {
        $this->parse($url);
    }

    private function parse(string $url){

        // remove trailing '/' from the url
        $url = trim($url, '/');

        // split it in pieces separated with '/'
        $pieces = explode('/', $url);
        $this->controller = $pieces[0] ?? 'default controller';
        $this->action = $pieces[1] ?? 'default action';
        $this->parameters = array_slice($pieces, 2);

        $this->load();
    }

    private function load(){
        //here come the Inversion Of Control, we'll load the controller and call the action with parameters on it

        //first let's ensure that it exists
        $format = ROOT . 'path' . DS . 'to' . DS . 'controllerFolder' . DS . '%sController.php';
        $controllerPath = sprintf($format, $this->controller); //sprintf to escape strings (%s)
        if(! file_exists($controllerPath)){
            $controllerPath = 'path/to/default/controller';
        }
        require_once $controllerPath;
        $controllerInstance = new $controllerPath(/* arguments for controller */);

        $action = $this->action;
        //let's now ensure that the method exists
        if(! method_exists($controllerInstance, $action)){
            $action = 'defaultAction';
        }

        call_user_func_array([$controllerInstance, $action], $this->parameters);
        //OR $contollerInstance->{$action}(... $this->parameters);
    }

}

//now let's call our frontController with our query from htaccess
new FrontController($_GET['query'] ?? '');
// url like http://localhost/film/view/1 will call filmController->view(1)


/**
 * RENDER A VIEW
 * with ob_start() ob_get_clean() functions and how to pass parameters
 */

class View{

    private $templatePath;

    function __construct(string $viewPath)
    {
        $this->templatePath = $viewPath;
    }

    public function render($parameters){

        //bufferize standard output for us, from now anything that outputs will be stocked in there
        ob_start();

        /*
        extract the vars from the array, key becomes variable name and value the value
        [
            'a' => 2
        ]
        will be accessible as $a and will have 2 as value

        EXTR_SKIP ensure that we don't overwrite other global variables
         */
        extract($parameters, EXTR_SKIP);

        //let's include the view and every php code will be evaluated and transformed with out variables from extract

        //require 'path/to/layout' OR header;
        require 'path/to/view' . $this->templatePath;
        //require 'path/to/layout' OR footer;

        //kill the buffer, and return it's content
        return ob_get_clean();
    }

}

//we will render like

$content = (new View('path/to/view.php'))->render([
    'greet' => 'Hi there'
]);

//and in the template at path/to/view.php
?>

<h1><?=$greet?> <!-- OR <?php echo $greet ?> --></h1>

<?php

//Hope that helps