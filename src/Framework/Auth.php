<?php

/*
 * CWF-PHP Framework
 * 
 * File: Auth.php
 * Description: Authentication framework
 * Author: Michał Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework;

use Exception;
use Framework\Config;
use Framework\Database\Connection;
use Framework\Database\Operator;
use Framework\Database\Query;
use Framework\Database\Statement;

enum Auth_Driver: string {

    // define drivers and method prefixes
    case Database = "Db";
    case LDAP = "Ldap";
}

trait Auth_Driver_Db {

    private static Connection $db_conn_handler;

    /**
     * Setup database connection for queries
     * 
     * @param string $connection
     * @return void
     */
    private static function Db_Setup(string $connection): void {
        self::$db_conn_handler = new Connection($connection);
    }

    /**
     * Db driver implementation for: AuthUser
     * 
     * @param string $login
     * @param string $password
     * @return bool
     */
    private static function Db_AuthUser(string $username, string $password): bool {
        $query = (new Query(Statement::SELECT))
                ->Table(self::$users_table)
                ->Where("username", Operator::Eq, $username);
        $result = self::$db_conn_handler->Query($query)->fetchAll();

        if (count($result) != 1) {

            return false;
        }

        return password_verify($password, $result[0]["password"]);
    }

    private static function Db_GroupExists(string $group): bool {
        $query = (new Query(Statement::SELECT))
                ->Table(self::$groups_table)
                ->Where("groupname", Operator::Eq, $group);

        $result = self::$db_conn_handler->Query($query)->fetchAll();

        return (count($result) == 1);
    }

    /**
     * Db driver implementation for: UserExists
     * 
     * @param string $login
     * @return bool
     */
    private static function Db_UserExists(string $username): bool {
        $query = (new Query(Statement::SELECT))
                ->Table(self::$users_table)
                ->Where("username", Operator::Eq, $username);

        $result = self::$db_conn_handler->Query($query)->fetchAll();

        return (count($result) == 1);
    }
}

class Auth {

    // import drivers
    use Auth_Driver_Db;

    private static ?Auth_Driver $driver = null;
    private static bool $is_init = false;
    private static string $connection;
    private static string $groups_table;
    private static string $memberships_table;
    private static string $users_table;

    /**
     * Loads configuration stored in CFGDIR/auth.json, if file is not present
     * does nothing
     * 
     * @return void
     * @throws Exception
     */
    public static function Load_Config(): void {
        if (self::$is_init) {
            throw new Exception("AUTH: Can be initialised only once");
        }
        // if no CFGDIR/auth.json file, do nothing
        if (!Config::Exists("auth")) {

            return;
        }

        $driver = strtolower(Config::Get("driver", "auth"));

        switch ($driver) {
            case "database":
                self::$driver = Auth_Driver::Database;
                break;
            case "ldap":
                //self::$driver = Auth_Driver::LDAP;
                throw new Exception("AUTH: LDAP driver is not implemented");
            default:
                throw new Exception("AUTH: Unknown driver");
        }

        self::$connection = Config::Get("connection", "auth");
        self::$groups_table = Config::Get("groups_table", "auth");
        self::$memberships_table = Config::Get("memberships_table", "auth");
        self::$users_table = Config::Get("users_table", "auth");
        self::$is_init = true;

        self::Call_Driver("Setup", self::$connection);
    }

    /**
     * Check, if given credentials are correct
     * 
     * @param string $login
     * @param string $password
     * @return bool
     */
    public static function AuthUser(string $username, string $password): bool {

        return self::Call_Driver("AuthUser", $username, $password);
    }

    public static function GroupExists(string $group): bool {

        return self::Call_Driver("GroupExists", $group);
    }

    /**
     * Check, if user exists for given login
     * 
     * @param string $login
     * @return bool
     */
    public static function UserExists(string $username): bool {

        return self::Call_Driver("UserExists", $username);
    }

    /**
     * Call the method of specified driver
     * 
     * @param string $function
     * @param type $params
     * @return mixed
     * @throws Exception
     */
    private static function Call_Driver(string $function, ...$params): mixed {
        if (!self::$is_init) {
            throw new Exception("AUTH: Not initialised");
        }

        $method = self::$driver->value . "_" . $function;

        if (!method_exists(self::class, $method)) {
            throw new Exception("AUTH: Undefined function '{$function}'");
        }

        return self::{$method}(...$params);
    }
}
