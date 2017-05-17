<?php
namespace Core\Mvc\Model;

use Core\Database\Orm\Schema\Table;
use Core\Mvc\Schema\Schema;

abstract class Model{

    private $_modified = [];

    protected $id;
    protected $schema;
    protected $table;

    private $hydrated;

    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
        $this->table = $schema->table();
    }

    public function __call($function, $args){

        $type = substr($function,0,3);
        $propName = strtolower(substr($function,3));
        $props = array_map(
            function($e) {
                return strtolower($e);
            },
            array_keys(get_class_vars(get_class($this)))
        );
        if(in_array($propName,$props)){
            switch($type){
                case 'get':
                    return $this->$propName;
                    break;
                case 'set':
                    if($args[0] !== $this->$propName){
                        $this->$propName = $args[0];
                        $this->_modified[$propName] = $args[0];
                    }
                    break;
            }
        }

        return null;
    }

    protected function setter($k, $v){
        if(! $this->hydrated)
            return;

        $propName = ucfirst($k);
        $propValue = $this->{"get{$propName}"}();

        if( $v !== $propValue ){
            $this->_modified[] = $k;
        }
    }

    public function isReady(){
        $this->hydrated = true;
    }

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->setter('id', $id);
        $this->id = $id;
    }

    public function getSchema(): Schema{
        return $this->schema;
    }

    public function getTable(): Table{
        return $this->table;
    }

    public function getSchemaDefintion(): array{
        return $this->schema->schema();
    }

    public function getModifications(): array{
        return $this->_modified;
    }

}