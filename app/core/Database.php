<?php

namespace app\core;

use app\core\Exception\InternalServerErrorException;
use Exception;

class Database
{

    public \PDO $pdo;
    public function __construct(array $config)
    {
        try{
            $dsn = $config['dsn'] ?? '';
            $user = $config['user'] ?? '';
            $password = $config['password'] ?? '';
            $this->pdo = new \PDO($dsn, $user, $password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }catch(Exception){
            throw new InternalServerErrorException();
        }
        
    }

    public function applyMigrations()
    {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();
        $files = scandir(Application::$ROOT_DIR . '/migrations');
        $toApplyMigrations = array_diff($files, $appliedMigrations);
        $newMigrations = [];
        foreach ($toApplyMigrations as $migration) {
            if ($migration === '.' || $migration === '..') continue;

            require_once Application::$ROOT_DIR . '/migrations/' . $migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);
            $instance = new $className();
            $this->log("Applying migration $migration");
            $instance->up();
            $this->log("Applied migration $migration");
            $newMigrations[] = $migration;
        }
        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        }
        $this->log("All migrations are applied.");
    }

    public function downMigrations()
    {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();
        $deleteMigrations = [];
        if (!empty($appliedMigrations)) {
            $appliedMigrations = array_reverse($appliedMigrations);
            foreach ($appliedMigrations as $migration) {
                require_once Application::$ROOT_DIR . '/migrations/' . $migration;
                $className = pathinfo($migration, PATHINFO_FILENAME);
                $instance = new $className();
                $this->log("rolling migration $migration");
                $instance->down();
                $this->log("rolled migration $migration");
                $deleteMigrations[] = $migration;
            }
        }
        if (!empty($deleteMigrations)) {
            $this->deleteMigrations($deleteMigrations);
        }
        $this->log("All tables are cleared.");
    }


    public function createMigrationsTable()
    {
        $sql = "
        create table if not exists migrations(
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            create_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=INNODB;
        ";
        $this->pdo->exec($sql);
    }

    public function getAppliedMigrations()
    {
        $statement = $this->pdo->prepare("select migration from migrations;");
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function saveMigrations(array $migrations)
    {
        $str = implode(',', array_map(fn ($m) => "('$m')", $migrations));
        $statement = $this->pdo->prepare("insert into migrations(migration) values$str;");
        $statement->execute();
    }

    public function deleteMigrations(array $migrations)
    {
        $str = implode(',', array_map(fn ($m) => "'$m'", $migrations));
        $statement = $this->pdo->prepare("delete from migrations where migration in ($str);");
        $statement->execute();
    }

    public function log($message)
    {
        echo '[' . date("Y-m-d h:i:s", time()) . '] - ' . $message . PHP_EOL;
    }

    public static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }
}