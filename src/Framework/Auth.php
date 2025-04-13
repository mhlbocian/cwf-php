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
use Framework\Database\{
    Connection,
    Operator,
    Query,
    Statement
};

enum Auth_Driver: string {

    // define drivers and method prefixes
    case Database = "Db";
    case LDAP = "Ldap";
}

trait Auth_Driver_Db {

    private static Connection $db_conn;
    private static string $db_conn_name;
    private static string $db_groups;
    private static string $db_memberships;
    private static string $db_users;

    /**
     * Setup database connection and driver-specific properties
     * 
     * @param string $connection
     * @return void
     */
    private static function Db_Setup(): void {
        self::$db_conn_name = self::$auth_cfg["connection"];
        self::$db_groups = self::$auth_cfg["groups_table"];
        self::$db_memberships = self::$auth_cfg["memberships_table"];
        self::$db_users = self::$auth_cfg["users_table"];
        self::$db_conn = new Connection(self::$db_conn_name);
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
                ->Table(self::$db_users)
                ->Where("username", Operator::Eq, $username);
        $result = self::$db_conn->Query($query)->fetchAll();

        if (count($result) != 1) {

            return false;
        }

        return password_verify($password, $result[0]["password"]);
    }

    private static function Db_GetGroups(): array {
        $output = [];

        $query = (new Query(Statement::SELECT))
                ->Table(self::$db_groups);
        $result = self::$db_conn->Query($query);

        foreach ($result as $row) {
            $output[$row["groupname"]] = $row["description"];
        }

        return $output;
    }

    private static function Db_GetUsers(?string $group): array {
        $output = [];

        if ($group == null) {
            $query = (new Query(Statement::SELECT))
                    ->Table(self::$db_users);
        } else {
            // TODO: QUERY JOIN OPERATIONS, now return empty array
            return $output;
        }

        $result = self::$db_conn->Query($query);

        foreach ($result as $row) {
            $output[$row["username"]] = $row["fullname"];
        }

        return $output;
    }

    private static function Db_GroupExists(string $groupname): bool {
        $query = (new Query(Statement::SELECT))
                ->Table(self::$db_groups)
                ->Where("groupname", Operator::Eq, $groupname);
        $result = self::$db_conn->Query($query)->fetchAll();

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
                ->Table(self::$db_users)
                ->Where("username", Operator::Eq, $username);
        $result = self::$db_conn->Query($query)->fetchAll();

        return (count($result) == 1);
    }

    private static function Db_UserInGroup(string $username, string $groupname): bool {
        $query = (new Query(Statement::SELECT))
                ->Table(self::$db_memberships)
                ->Where("username", Operator::Eq, $username)
                ->And("groupname", Operator::Eq, $groupname);
        $result = self::$db_conn->Query($query)->fetchAll();

        return (count($result) == 1);
    }
}

class Auth {

    // import drivers
    use Auth_Driver_Db;

    private static ?Auth_Driver $driver = null;
    private static array $auth_cfg;
    private static bool $is_init = false;

    /**
     * Loads configuration stored in CFGDIR/auth.json, if file is not present
     * does nothing
     * 
     * @return void
     * @throws Exception
     */
    public static function Init(): void {
        if (self::$is_init) {
            throw new Exception("AUTH: Can be initialised only once");
        }
        // if no CFGDIR/auth.json file, do nothing
        if (!Config::Exists("auth")) {

            return;
        }

        self::$auth_cfg = Config::Fetch("auth");
        $driver = strtolower(self::$auth_cfg["driver"]);

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

        self::$is_init = true;
        self::Call_Driver("Setup");
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

    public static function GetGroups(): array {

        return self::Call_Driver("GetGroups");
    }

    public static function GetUsers(?string $groupname = null): array {

        return self::Call_Driver("GetUsers", $groupname);
    }

    public static function GroupExists(string $groupname): bool {

        return self::Call_Driver("GroupExists", $groupname);
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

    public static function UserInGroup(string $username, string $groupname): bool {

        return self::Call_Driver("UserInGroup", $username, $groupname);
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
        // set method name with driver's prefix
        $method = self::$driver->value . "_" . $function;

        if (!method_exists(self::class, $method)) {
            throw new Exception("AUTH: Undefined function '{$function}'");
        }

        return self::{$method}(...$params);
    }
}
