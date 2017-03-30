<?php

namespace Core\Command\Files;

class EntityGenerator{

    private $schema;
    private $name;
    private $output;
    private $force;

    public function __construct($name, $schema, $force = false)
    {
        $this->schema = $schema;
        $this->name = ucfirst($this->normalize($name));
        $this->force = $force;
        $this->output = '';
        $this->build();
    }

    private function normalize($str){
        return lcfirst(str_replace(['_','-'],'',ucwords(strtolower($str), '_-')));
    }

    private function arrayToString($array){
        $o = [];
        foreach ($array as $k => $v){
            if(is_array($v))
                $v = $this->arrayToString($v);
            elseif (!preg_match('#^\d+$#',$v))
                $v = empty($v) ? "NULL" :  "\"{$v}\"";
            if(!preg_match('#^\d+$#',$k))
                $k = "\"{$k}\"";
            $o[] = "{$k} => {$v}";
        }
        return 'Array ( ' . implode(', ',$o) . ' )';
    }

    private function build(){
        $this->output = $this->generateClass();
        $this->generate();
    }

    private function generate(){
        $fileName = ENTITY . "{$this->name}Entity.php";
        if(file_exists($fileName)){
            if($this->force){
                echo "[OVERRIDDING] Entity {$this->name} at {$fileName}\n";
				file_put_contents($fileName, $this->output);
            }
            else{
                echo "[SKIPPING] Entity {$this->name} at {$fileName}\n";
            }
        }
        else{
			if(!is_dir(ENTITY))
				mkdir(ENTITY);
			
            echo "[CREATING] Entity {$this->name} at {$fileName}\n";
			file_put_contents($fileName, $this->output);
        }
        
    }

    private function generateClass(){
        return "<?php\n\nnamespace App\\Entity;\n\nuse Core\\Mvc\\Entity\\Entity;\n\nclass {$this->name}Entity extends Entity{\n\n{$this->generateProperties()}\n\n{$this->generateConstructor()}\n\n{$this->generateSettersAndGetters()}\n\n}";
    }

    private function generateProperties(){
        $out = [];
        foreach ($this->schema as $property){
            $out[] = $this->generateProperty($property);
        }
        return "    " . implode("\n    ",array_merge($out, $this->generateBaseProperty()));
    }

    private function generateProperty($property){
        return "public \${$this->normalize($property['name'])}" . ($property['value'] ? " = {$property['value']}" : '') . ";";
    }

    private function generateBaseProperty(){
        return [
            "public \$_schema = {$this->arrayToString($this->schema)};"
        ];
    }

    private function generateConstructor(){
        return "    public function __construct(){\n        parent::__construct(func_get_args());\n    }";
    }

    private function generateSettersAndGetters(){
        $out = [];
        foreach ($this->schema as $property){
            $out[] = $this->generateGetterAndSetter($property['name']);
        }
        return "    " . implode("\n\n    ",$out);
    }

    private function generateGetterAndSetter($propName){
        return $this->generateGetter($propName) . "\n\n    " . $this->generateSetter($propName);
    }

    private function generateSetter($propName){
        $propUpper = ucfirst($propName);
        return "public function set{$propUpper}(\${$propName}){\n        parent::setter('{$propName}',\${$propName});\n        \$this->{$propName} = \${$propName};\n    }";
    }

    private function generateGetter($propName){
        $propUpper = ucfirst($propName);
        return "public function get{$propUpper}(){\n        return \$this->{$propName};\n     }";
    }

}