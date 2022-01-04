<?php

namespace App\ORM;

use App\Controller\AppConfig;
use PDO;

abstract class Entity {

    protected $tableName;

    protected $db;

    public function __construct()
    {
        try {
            $dsn = 'mysql:dbname=' . AppConfig::get('dbName') . ';host=' . AppConfig::get('dbHost');
            $this->db = new PDO($dsn, AppConfig::get('dbUsername'), AppConfig::get('dbPassword'));
        } catch (\Exception $e) {
            throw new \Exception('Error creating a database connection ');
        }
    }

    public function __get($name): mixed
    {
        return $this->$name;
    }

    public function __set($name, $value): void
    {
        $this->$name = $value;
    }

    public function save(): bool
    {
        $class = new \ReflectionClass($this);
        $tableName = '';

        if ($this->tableName != '') {
            $tableName = $this->tableName;
        } else {
            $tableName = strtolower($class->getShortName());
        }

        $propsToImplode = [];

        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $propertyName = $property->getName();
            $value = $this->{$propertyName};

            if ($propertyName !== 'id') {
                $propsToImplode[] = '`'.$propertyName.'` = "'.$value.'"';
            }
        }

        $setClause = implode(',',$propsToImplode);
        $sqlQuery = '';

        if ($this->id > 0) {
            $sqlQuery = 'UPDATE `'.$tableName.'` SET '.$setClause.' WHERE id = '.$this->id;
        } else {
            $sqlQuery = 'INSERT INTO `'.$tableName.'` SET '.$setClause;
        }

        $res = boolval($this->db->exec($sqlQuery));

        $this->id = intval($this->db->lastInsertId());

        return $res;
    }

    private function hydrate(array $object): mixed
    {
        $class = new \ReflectionClass(get_called_class());

        $entity = $class->newInstance();

        foreach($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $prop) {
            if (isset($object[$prop->getName()])) {
                $prop->setValue($entity,$object[$prop->getName()]);
            }
        }

        return $entity;
    }

    public static function find($options = []): mixed
    {
        $self = new static;

        $query = 'SELECT * FROM ' . $self->tableName;
        $results = [];

        if (!empty($options)) {
            if (is_array($options)) {
                foreach ($options as $key => $value) {
                    $whereConditions[] = '`'.$key.'` = "'.$value.'"';
                }

                $query .= " WHERE ".implode(' AND ',$whereConditions);
            } else {
                $query .= ' WHERE '.$options;
            }
        }

        $raw = $self->db->query($query);

        foreach ($raw as $rawRow) {
            if ($raw->rowCount() < 1) {
                $results = null;
            } else if ($raw->rowCount() === 1) {
                $results = $self->hydrate($rawRow);
            } else {
                $results[] = $self->hydrate($rawRow);
            }
        }

        return $results;
    }

    public static function delete($id): bool
    {
        $self = new static;

        $query = 'DELETE FROM ' . $self->tableName . ' WHERE id = :id';
        $statement = $self->db->prepare($query);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        return $statement->execute();
    }
}