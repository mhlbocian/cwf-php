<?php

/*
 * CWF-PHP Framework
 * 
 * File: Framework\Connection.php
 * Description: Database connections class
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework;

use Framework\Config;

class Database {

    /**
     * 
     * @var PDO Connection handler
     */
    private \PDO $pdo;

    /**
     * Directory for SQLite files
     */
    private const DBDIR = \DATADIR . \DS;

    /**
     * 
     * @var string Database name
     */
    private string $dbname;

    /**
     * 
     * @var string PDO dsn
     */
    private string $dsn;

    /**
     * 
     * @var string Database host
     */
    private string $host;

    /**
     * 
     * @var string|null Database password
     */
    private ?string $password;

    /**
     * 
     * @var string Database port
     */
    private string $port;

    /**
     * 
     * @var string|null Database username
     */
    private ?string $username;

    /**
     * 
     * @var string Connection data for specified name in `CFGDIR/database.json`
     */
    public readonly string $conn_name;

    /**
     * 
     * @var string Database driver
     */
    public readonly string $driver;

    /**
     * Establish database connection. Fetches configuration from
     * `CFGDIR/database.json`. When $conn_name is null, loads `default` section
     * 
     * @param string $conn_name
     */
    public function __construct(string $conn_name = "default") {
        $db_cfg = Config::Get("database", $conn_name);
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

    /**
     * Execute a query
     * 
     * @param Query $query
     * @return PDOStatement
     */
    public function Query(Query $query): \PDOStatement {
        $prep = $this->pdo->prepare($query);

        foreach ($query->Params() as $id => $param) {
            $prep->bindValue(":{$id}", $param);
        }

        $prep->execute();

        return $prep;
    }

    /**
     * Create PDO `dsn` for specific connection type
     * 
     * @return void
     * @throws Exception
     */
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
