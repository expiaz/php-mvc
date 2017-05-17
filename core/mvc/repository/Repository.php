<?php

namespace Core\Mvc\Repository;

use Core\Database\Orm\Schema\Table;
use Core\Mvc\Schema\Schema;
use Core\Database\Database;
use Core\Mvc\Model\Model;
use Core\Utils\DataContainer;


abstract class Repository{

    protected $database;
    protected $schema;
    protected $table;
    protected $model;

    public function __construct(Database $db, string $modelNs, Schema $schema)
    {
        $this->database = $db;
        $this->schema = $schema;
        $this->table = $this->schema->table();
        $this->model = $modelNs;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function getSchema(): Schema
    {
        return $this->schema;
    }

    public function getModel(): Model
    {
        return (new $this->model($this->schema));
    }

    private function hydrate(DataContainer $class): Model
    {
        $model = $this->getModel();
        $schema = $model->getSchemaDefintion();
        foreach ($schema['fields'] as $field){
            if(property_exists($model, $field['name']))
                $model->{ "set" . ucfirst( $field['name'] ) }( $class->{ "get" . ucfirst( $field['name'] ) }() );
        }
        $model->isReady();
        return $model;
    }

    public function fetch(string $sql, array &$param = []): Model
    {
        $upplet = $this->database->fetch($sql, $param);
        $model = $this->hydrate($upplet);
        return $model;
    }

    public function fetchAll(string $sql, array &$param = []): array
    {
        $upplets = $this->database->fetchAll($sql, $param);
        return array_map(function(DataContainer $e){
            return $this->hydrate($e);
        }, $upplets);
    }

    public function getLastInsertId()
    {
        return $this->database->getLastInsertId();
    }

    public function find($clause, $bind): array
    {
        $sql = 'SELECT ' . (isset($clause['select']) ? is_array($clause['select']) ? implode(', ',$clause['select']) : $clause['select'] : '*') . ' ';
        $sql .= 'FROM ' . (isset($clause['from']) ? is_array($clause['from']) ? implode(', ', $clause['from']) : $clause['from'] : $this->table) . ' ';
        $sql .= isset($clause['where']) ? ( 'WHERE ' . (is_array($clause['where']) ? implode(' AND ', $clause['where']) : $clause['where'])) : '';
        $sql .= isset($clause['orderby']) ? ( 'ORDER BY ' . (is_array($clause['orderby']) ? implode(', ', $clause['orderby']) : $clause['orderby'])) : '';
        $sql .= isset($clause['groupby']) ? ( 'GROUP BY ' . (is_array($clause['groupby']) ? implode(', ', $clause['groupby']) : $clause['groupby'])) : '';
        $sql .= isset($clause['having']) ? ( 'HAVING ' . (is_array($clause['having']) ? implode(' AND ', $clause['having']) : $clause['having'])) : '';
        $sql .= isset($clause['limit']) ? ( 'LIMIT ' . (is_array($clause['limit']) ? implode(', ', $clause['limit']) : $clause['limit'])) : '';
        $sql.= ';';
        return $this->database->fetchAll($sql, $bind);
    }


    public function getAll(): array
    {
        $sql = sprintf("SELECT * FROM %s;",$this->table);
        return $this->fetchAll($sql);
    }

    public function getByField($field, $value): array
    {
        $sql = sprintf("SELECT * FROM %s WHERE %s = :value;",$this->table, $field);
        $parameters = ['value' => $value];
        return $this->fetchAll($sql,$parameters);
    }

    public function getByFields(array $fields, array &$values): array
    {
        $where = implode(' AND ',array_map(function($e){
            return sprintf("%s = :%s",$e,$e);
        }, $fields));
        $sql = sprintf("SELECT * FROM %s WHERE %s;",$this->table, $where);
        return $this->fetchAll($sql,$values);
    }

    public function getById($id): Model
    {
        $sql = sprintf("SELECT * FROM %s WHERE id = :id;",$this->table);
        $parameters = ['id' => $id];
        return $this->fetch($sql,$parameters);
    }


    public function persist(Model $o)
    {
        if(is_null($o->getId())){
            return $this->update($o);
        }

        $shouldUpdate = $this->find([
            'select' => ' COUNT(id) as nb',
            'from' => $o->getTable()->getName(),
            'where' => 'id = :id'
        ], [
            'id' => $o->getId()
        ]);

        if($shouldUpdate[0]->nb > 0){
            return $this->update($o);
        }

        return $this->insert($o);
    }

    public function update(Model $o)
    {
        $modifications = $o->getModifications();

        if(count($modifications) === 0){
            return false;
        }

        $fields = implode(' AND ',
            array_map(
                function(string $e):string {
                    return sprintf("%s = :%s",$e,$e);
                },
                $modifications
            )
        );

        $sql = sprintf("UPDATE %s SET %s WHERE id=%d;", $o->getTable()->getName(), $fields, $o->getId());

        $values = [];
        foreach ($modifications as $field){
            $values[$field] =  $o->{"get" . ucfirst($field)}();
        }

        /*
        echo "update\n";
        echo "values " . print_r($values, true) . "\n";;
        echo "sql $sql\n";
        */

        return $this->database->execute($sql, $values) ? $this->getLastInsertId() : false;
    }

    public function insert(Model $o)
    {
        $fieldsSchema = $o->getSchemaDefintion()['fields'];

        $fields = array_map(function($e){
            return sprintf("%s",$e['name']);
        }, $fieldsSchema);

        if(count($fields) === 0)
            return false;

        $values = [];
        foreach ($fieldsSchema as $fieldSchema){
            $name = ucfirst($fieldSchema['name']);
            $values[$fieldSchema['name']] =  $o->{"get{$name}"}();
        }

        $sql = sprintf("INSERT INTO `%s` (`%s`) VALUES (:%s)", $o->getTable()->getName(), implode('`, `', $fields), implode(', :', $fields));

        /*
        echo "insert\n";
        echo "values " . print_r($values, true) . "\n";;
        echo "sql $sql\n";
        return;
        */

        if($this->database->execute($sql, $values)){
            $o->setId($this->getLastInsertId());
            return true;
        }
        return false;
    }

}