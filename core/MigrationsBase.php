<?php
namespace core;

use PDO;

abstract class MigrationsBase{
    protected static ?PDO $CONNECTION = Null;

    final public function __construct(){
        $this->CONNECTION = Application::APP()->database->connector(); 
    }

    public function connection(){
        return $this->CONNECTION;
    }

    abstract function up();
    abstract function down();

}