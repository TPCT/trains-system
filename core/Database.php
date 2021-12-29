<?php
namespace core;

use core\Exceptions\DatabaseError;
use PDO;

class Database{
    protected ?PDO $connection;

    public function __construct(){
        $this->connection = Null;
        if (isset($_ENV['DB_DRIVER'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS'])){
            $dsn = "{$_ENV['DB_DRIVER']}:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}";
            $db_user = $_ENV['DB_USER'];
            $db_pass = $_ENV['DB_PASS'];
            $this->connection = new PDO($dsn, $db_user, $db_pass,[
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    public function connector(){
        return $this->connection;
    }

    public static function CREATE_DB(array $config){
        if ($config){
            $dsn = explode(';', $config['dsn']) ?? [];
            $db_name = explode('=', $dsn[1])[1] ?? '';
            $db_user = $config['db_user'] ?? '';
            $db_pass = $config['db_pass'] ?? '';
            $dsn = $dsn[0] ?? '';

            $connector = new PDO($dsn, $db_user, $db_pass,[
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            $query = "CREATE DATABASE IF NOT EXISTS `:db`;";
            $stmt = $connector->prepare($query);
            $stmt->bindParam(":db", $db_name);
            return $stmt->execute();
        }
    }

    public function applyMigrations(){
        if (!$this->connection) throw new DatabaseError();
        $appliedMigrations = array();
        $this->createMigrationsTable();
        $applied_migrations = $this->getAppliedMigrations();

        $files = \scandir(Application::ROOT_DIR() . \DIRECTORY_SEPARATOR . "migrations");
        $toApplyMigrations = \array_diff($files, $applied_migrations);
        foreach($toApplyMigrations as $migration){
            if ($migration === '.' || $migration === '..')
                continue;
            require_once(Application::ROOT_DIR() . \DIRECTORY_SEPARATOR . "migrations" . \DIRECTORY_SEPARATOR . $migration);
            $class = "migrations\\" . \pathinfo($migration, \PATHINFO_FILENAME);
            $class = new $class();
            $class->up();
            $appliedMigrations[] = $migration;
        }
        if ($appliedMigrations){
            $this->saveMigrations($appliedMigrations);
        }
    }

    private function getAppliedMigrations(){
        $query = "SELECT migration FROM migrations";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function createMigrationsTable(){
        $this->connection->exec(
            "CREATE TABLE IF NOT EXISTS migrations(
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=INNODB;"
        );
    }

    private function saveMigrations(array $migrations){
        foreach($migrations as $migration){
            $query = "INSERT INTO migrations(migration) VALUES (:migration_name)";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(":migration_name", $migration);
            $stmt->execute();
        }
    }
}