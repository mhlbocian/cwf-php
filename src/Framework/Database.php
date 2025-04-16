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
     * 
     * @var string Database name
     */
    private string $database;
    
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
        $this->driver = strtolower($db_cfg["driver"]);
        $this->database = $db_cfg["database"];
        // other fields depends on database driver
        $this->host = $db_cfg["host"] ?? "";
        $this->port = $db_cfg["port"] ?? "";
        $this->username = $db_cfg["username"] ?? null;
        $this->password = $db_cfg["password"] ?? null;
        // create dsn and perform connection
        $this->Create_Dsn();
        $this->pdo = new \PDO($this->dsn, $this->username, $this->password);
    }

    /**
     * Create PDO `dsn` for specific connection type
     * 
     * @return void
     * @throws Exception
     */
    private function Create_Dsn(): void {
        switch ($this->driver) {
            case "firebird":
                throw new \Exception("DB: Firebird is not supported");
            case "mysql":
                $this->dsn = "mysql:host={$this->host};"
                        . "dbname={$this->database}";
                break;
            case "pgsql":
                throw new \Exception("DB: PostgreSQL is not supported");
            case "sqlite":
                $this->dsn = "sqlite:" . DATADIR . DS . $this->database;
                break;
            default:
                throw new \Exception("DB: unknown driver '{$this->driver}'");
        }
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
}
