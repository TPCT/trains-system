<?php

namespace core;

use PDOException;

abstract class DBModel extends Model{
    public abstract function tableName(): string;
    public abstract function attributes(): array;

    public function save(){
        try{
            $tableName = $this->tableName();
            $attributes = $this->attributes();
            $params = \array_map(fn($attribute) => ":{$attribute}", $attributes);
            $query = "INSERT INTO {$tableName} (" . \implode(', ', $attributes) . ") VALUES (" . \implode(', ', $params) . ")";
            $stmt = $this->prepare($query);
            
            foreach ($attributes as $attribute){
                $stmt->bindParam(":{$attribute}", $this->{$attribute});
            }

            $stmt->execute();
            return True;
        }catch(PDOException $e){
            return False;
        }
    }

    public function prepare($query){
        return Application::APP()->database->connector()->prepare($query);
    }
}