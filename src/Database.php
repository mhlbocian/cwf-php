<?php

/*
 * CWF-PHP Framework
 * 
 * File: Connection.php
 * Description: Database connections class
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Mhlbocian\CwfPhp;

use Mhlbocian\CwfPhp\Config;
use Mhlbocian\CwfPhp\Interfaces\Database as IDatabase;

final class Database implements IDatabase {

    private const DBDIR = \DATADIR . \DS;

    private \PDO $pdo;
    private string $dbname;
    private string $dsn;
    private string $host;
    private ?string $password;
    private string $port;
    private ?string $username;
    private string $conn_name;
    public readonly string $driver;
    
    #[\Override]
    public function __construct(string $conn_name = "default") {
        $db_cfg = Config::File("database")->Get($conn_name);
        $this->conn_name = $conn_name;
        // both driver and database cannot be empty
        $this->driver = \strtolower($db_cfg["driver"]);
        $this->dbname = $db_cfg["database"];
        // other fields depends on database driver
        $this->host = $db_cfg["host"] ?? "";
        $this->port = $db_cfg["port"] ?? "";
        $this->username = $db_cfg["username"] ?? null;
        $this->password = $db_cfg["password"] ?? null;
        // create dsn and perform connection
        $this->DSN_Set();
        $this->pdo = new \PDO($this->dsn, $this->username, $this->password);
    }
    
    #[\Override]
    public function Query(Query $query): \PDOStatement {
        $prep = $this->pdo->prepare($query);

        foreach ($query->Params() as $id => $param) {
            $prep->bindValue(":{$id}", $param);
        }

        $prep->execute();

        return $prep;
    }
    
    private function DSN_Set(): void {
        try {
            $this->dsn = match ($this->driver) {
                "mysql" => "mysql:host={$this->host};dbname={$this->dbname}",
                "pgsql" => "pgsql:host={$this->host};dbname={$this->dbname}",
                "sqlite" => "sqlite:" . self::DBDIR . "{$this->dbname}"
            };
        } catch (\UnhandledMatchError) {
            
            throw new \Exception("DB: Unknown driver `{$this->driver}`");
        }
    }
}
