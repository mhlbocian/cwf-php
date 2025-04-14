<?php

/*
 * CWF-PHP Framework
 * 
 * File: Driver_Db.php
 * Description: Authentication framework - Database Driver
 * Author: Michał Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Auth;

use Framework\{
    Database,
    Query
};
use Framework\Query\{
    Operator,
    Statement
};

trait Driver_Db {

    /**
     * 
     * @var Database Connection handler
     */
    private static Database $db_conn;

    /**
     * 
     * @var string Connection name
     */
    private static string $db_conn_name;
    /**
     * 
     * @var string Table name for groups data
     */
    private static string $db_groups;
    
    /**
     * 
     * @var string Table name for memberships data
     */
    private static string $db_memberships;
    
    /**
     * 
     * @var string Table name for users data
     */
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
        self::$db_conn = new Database(self::$db_conn_name);
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

    /**
     * Db driver implementation for: GetGroups
     * 
     * @return array
     */
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

    /**
     * Db driver implementation for: GetUsers
     * 
     * @param string|null $group
     * @return array
     */
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

    /**
     * Db driver implementation for: GroupExists
     * 
     * @param string $groupname
     * @return bool
     */
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

    /**
     * Db driver implementation for: UserInGroup
     * 
     * @param string $username
     * @param string $groupname
     * @return bool
     */
    private static function Db_UserInGroup(string $username, string $groupname): bool {
        $query = (new Query(Statement::SELECT))
                ->Table(self::$db_memberships)
                ->Where("username", Operator::Eq, $username)
                ->And("groupname", Operator::Eq, $groupname);
        $result = self::$db_conn->Query($query)->fetchAll();

        return (count($result) == 1);
    }
}
