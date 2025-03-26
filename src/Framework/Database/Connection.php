<?php

/*
 * Custom Web Framework
 * 
 * Author: Michał Bocian <mhl.bocian@gmail.com>
 * License: 3-clause BSD
 */

namespace Framework\Database;

use Exception;
use PDO;
use Framework\Config;

class Connection {

    // Data directory for SQLite files
    private const string DATADIR = APPDIR . DS . "Data";

    private string $driver;
    private string $database;
    private string $host;
    private string $port;
    private ?string $username;
    private ?string $password;
    private string $dsn;
    private PDO $pdo;

    public function __construct(string $conn_name = "default") {
        $dbcfg = Config::Get($conn_name, "database");
        // both driver and database cannot be empty
        $this->driver = $dbcfg["driver"];
        $this->database = $dbcfg["database"];
        // other fields depends on database driver
        $this->host = $dbcfg["host"] ?? "";
        $this->port = $dbcfg["port"] ?? "";
        $this->username = $dbcfg["username"] ?? null;
        $this->password = $dbcfg["password"] ?? null;

        // create dsn and perform connection
        $this->CreateDsn();
        $this->pdo = new PDO($this->dsn, $this->username, $this->password);
    }
    
    // temporary function, removed in the future
    public function PDO(): PDO {
        return $this->pdo;
    }

    private function CreateDsn(): void {
        switch ($this->driver) {
            case "firebird":
            case "mysql":
            case "pgsql":
                throw new Exception("Unimplemented database driver");
            case "sqlite":
                $this->dsn = "sqlite:" . self::DATADIR . DS . $this->database;
                break;
            default:
                throw new Exception("Unknown database driver: {$this->driver}");
        }
    }
}
