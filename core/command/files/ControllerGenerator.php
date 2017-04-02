<?php

namespace Core\Command\Files;

class ControllerGenerator{

    private $name;
    private $output;
    private $force;

    public function __construct($name, $force = false)
    {
        $this->name = ucfirst($this->normalize($name));
        $this->force = $force;
        $this->output = '';
        $this->build();
    }

    private function normalize($str){
        return lcfirst(str_replace(['_','-'],'',ucwords(strtolower($str), '_-')));
    }

    private function build(){
        $this->output = $this->generateClass();
        $this->generate();
    }

    private function generate(){
        $fileName = CONTROLLER . "{$this->name}Controller.php";
        if(file_exists($fileName)){
            if($this->force){
                echo "[OVERRIDDING] Controller {$this->name} at {$fileName}\n";
				file_put_contents($fileName, $this->output);
            }
            else{
                echo "[SKIPPING] Controller {$this->name} at {$fileName}\n";
            }
        }
        else{
			if(!is_dir(CONTROLLER))
				mkdir(CONTROLLER);
			
            echo "[CREATING] Controller {$this->name} at {$fileName}\n";
			file_put_contents($fileName, $this->output);
        }
        
    }

    private function generateClass(){
        return "<?php\n\nnamespace App\\Controller;\n\nuse Core\\Mvc\\Controller\\Controller;\nuse Core\\Http\\Request;\n\nclass {$this->name}Controller extends Controller{\n\n{$this->generateConstructor()}\n\n{$this->generateMethod()}\n\n}";
    }

    private function generateMethod(){
        return "    public function index(Request \$request, ...\$parameters){\n        //Your logic here\n    }";
    }

    private function generateConstructor(){
        return "    public function __construct(){\n        parent::__construct();\n    }";
    }

}