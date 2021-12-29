<?php
namespace core\Exceptions;

use Exception;

class DatabaseError extends Exception{
    public function __construct(){
        $this->message = "There's something wrong with pdo connection please check .env file\n";
    }
}