<?php

namespace Core\Command\Files;

class ModelGenerator{

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
        $fileName = MODEL . "{$this->name}Model.php";
        if(file_exists($fileName)){
            if($this->force){
                echo "[OVERRIDDING] Model {$this->name} at {$fileName}\n";
				file_put_contents($fileName, $this->output);
            }
            else{
                echo "[SKIPPING] Model {$this->name} at {$fileName}\n";
            }
        }
        else{
			if(!is_dir(MODEL))
				mkdir(MODEL);
			
            echo "[CREATING] Model {$this->name} at {$fileName}\n";
			file_put_contents($fileName, $this->output);
        }
    }

    private function generateClass(){
        return "<?php\n\nnamespace App\\Model;\n\nuse Core\\Mvc\\Model\\Model;\n\nclass {$this->name}Model extends Model{\n\n{$this->generateConstructor()}\n\n}";
    }

    private function generateConstructor(){
        return "    public function __construct(){\n        parent::__construct();\n    }";
    }

}