<?php

namespace core;

abstract class Model{
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_UNIQUE = 'unique';
    public const RULE_MATCH = 'match';

    public array $errors = [];

    public function loadData($data){
        foreach ($data as $key => $value){
            if (\property_exists($this, $key))
                $this->{$key} = $value;
        }
    }

    abstract function rules(): array;
    abstract static function tableName(): string;

    public function Validate(){
        foreach($this->rules() as $attribute => $rules){
            $value = $this->{$attribute};
            foreach ($rules as $rule){
                $ruleName = $rule;
                if (is_array($rule)){
                    $ruleName = $rule[0];
                }

                if ($ruleName === SELF::RULE_REQUIRED && $value == NULL){
                    $this->addError($attribute, self::RULE_REQUIRED);
                }   

                if ($ruleName === self::RULE_EMAIL && !filter_var($value, \FILTER_VALIDATE_EMAIL)){
                    $this->addError($attribute, self::RULE_EMAIL);
                }

                if ($ruleName === self::RULE_MIN && \strlen($value) < $rule['min']){
                    $this->addError($attribute, SELF::RULE_MIN, $rule);
                }

                if ($ruleName === self::RULE_MAX && \strlen($value) > $rule['max']){
                    $this->addError($attribute, SELF::RULE_MIN, $rule);
                }

                if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}){
                    $this->addError($attribute, SELF::RULE_MATCH, $rule);
                }

                if ($ruleName === self::RULE_UNIQUE){
                    $model = $rule['model'];
                    $tableName = $model::tableName();
                    $query = "SELECT * FROM {$tableName} WHERE {$attribute} = :attr";
                    $stmt = $this->prepare($query);
                    $stmt->bindParam(':attr', $value);
                    $stmt->execute();
                    $record = $stmt->fetchObject();
                    if ($record){
                        $this->addError($attribute, SELF::RULE_UNIQUE, $rule);
                    }
                }
            }
        }

        return empty($this->errors);
    }


    public function prepare($query){
        return Application::APP()->database->connector()->prepare($query);
    }
    
    public function addError(string $attribute, string $rule, $params = []){
        $message = $this->errorMessages()[$rule] ?? '';
        foreach($params as $key => $value){
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute][] = $message;
    }

    public function errorMessages(){
        return [
            self::RULE_REQUIRED => 'This field is required',
            self::RULE_EMAIL => 'This field must be valid email address',
            self::RULE_MIN => 'Min length of this field must be {min}',
            self::RULE_MAX => 'Max length of this field must be {max}',
            self::RULE_MATCH => "This field must be the same as {match}",
            SELF::RULE_UNIQUE => "This field has been registered before"
        ];
    }
    
    public function hasError($attribute){
        return isset($this->errors[$attribute]) ? true : false;
    }

    public function getFirstError($attribute){
        return isset($this->errors[$attribute]) ? $this->errors[$attribute][0] : '';
    }
}