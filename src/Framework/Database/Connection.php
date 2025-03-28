<?php

/*
 * CWF-PHP Framework
 * 
 * File: Connection.php
 * Description: Framework\Database\Connection class
 * Author: Michał Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Database;

use Exception;
use PDO;
use PDOStatement;
use Framework\Config;

class Connection {

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
        $this->Create_Dsn();
        $this->pdo = new PDO($this->dsn, $this->username, $this->password);
    }

    public function Query(Query $query): PDOStatement {
        $prep = $this->pdo->prepare($query);

        foreach ($query->Get_Params() as $id => $param) {
            $prep->bindValue(":{$id}", $param);
        }

        $prep->execute();

        return $prep;
    }

    private function Create_Dsn(): void {
        switch ($this->driver) {
            case "firebird":
            case "mysql":
            case "pgsql":
                throw new Exception("Unimplemented database driver");
            case "sqlite":
                $this->dsn = "sqlite:" . DATADIR . DS . $this->database;
                break;
            default:
                throw new Exception("Unknown database driver: {$this->driver}");
        }
    }
}
