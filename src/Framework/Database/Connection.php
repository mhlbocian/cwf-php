<?php

/*
 * CWF-PHP Framework
 * 
 * File: Connection.php
 * Description: Database connections class
 * Author: Michał Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Database;

use Exception;
use PDO;
use PDOStatement;
use Framework\Config;

class Connection {

    private PDO $pdo;
    private string $database;
    private string $driver;
    private string $dsn;
    private string $host;
    private ?string $password;
    private string $port;
    private ?string $username;

    /**
     * Estabilish database connection. Fetches configuration from database.json.
     * When $conn_name is null, loads "default" section
     * 
     * @param string $conn_name
     */
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

    /**
     * Creates PDO 'dsn' for specific connection type
     * 
     * @return void
     * @throws Exception
     */
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

    /**
     * Executes query.
     * 
     * @param Query $query
     * @return PDOStatement
     */
    public function Query(Query $query): PDOStatement {
        $prep = $this->pdo->prepare($query);

        foreach ($query->Params() as $id => $param) {
            $prep->bindValue(":{$id}", $param);
        }

        $prep->execute();

        return $prep;
    }
}
