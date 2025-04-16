<?php

/*
 * CWF-PHP Framework
 * 
 * File: Driver_Db.php
 * Description: AUTH API - Database Driver
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

trait Driver_Database {

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
     * Db driver implementation for: GroupAdd
     * 
     * @param string $groupname
     * @param string $description
     * @return Status
     */
    private static function Db_GroupAdd(
            string $groupname,
            string $description): Status {

        try {
            $query = (new Query(Statement::INSERT))
                    ->Table(self::$db_groups)
                    ->Columns("groupname", "description")
                    ->Values($groupname)
                    ->Values($description);
            self::$db_conn->Query($query);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }

    /**
     * Db driver implementation for: GroupChDesc
     * 
     * @param string $groupname
     * @param string $description
     * @return Status
     */
    private static function Db_GroupChDesc(
            string $groupname,
            string $description): Status {

        try {
            $query = (new Query(Statement::UPDATE))
                    ->Table(self::$db_groups)
                    ->Columns("groupname", "description")
                    ->Values($groupname, $description)
                    ->Where("groupname", Operator::Eq, $groupname);
            self::$db_conn->Query($query);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }

    /**
     * Db driver implementation for: GroupDel
     * 
     * @param string $groupname
     * @return Status
     */
    private static function Db_GroupDel(string $groupname): Status {
        try {
            // remove membership relations
            $query = (new Query(Statement::DELETE))
                    ->Table(self::$db_memberships)
                    ->Where("groupname", Operator::Eq, $groupname);
            self::$db_conn->Query($query);
            // remove the group
            $query = (new Query(Statement::DELETE))
                    ->Table(self::$db_groups)
                    ->Where("groupname", Operator::Eq, $groupname);
            self::$db_conn->Query($query);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
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
     * Db driver implementation for: GroupFetch
     * 
     * @return array
     */
    private static function Db_GroupFetch(): array {
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
     * Db driver implementation for: UserAdd
     * 
     * @param string $username
     * @param string $fullname
     * @param string $password
     * @return Status
     */
    private static function Db_UserAdd(
            string $username,
            string $fullname,
            string $password): Status {

        $query = (new Query(Statement::INSERT))
                ->Table(self::$db_users)
                ->Columns("username", "fullname", "password")
                ->Values($username)
                ->Values($fullname)
                ->Values(password_hash($password, PASSWORD_DEFAULT));

        try {
            self::$db_conn->Query($query);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }

    /**
     * Db driver implementation for: UserAuth
     * 
     * @param string $login
     * @param string $password
     * @return bool
     */
    private static function Db_UserAuth(
            string $username,
            string $password): bool {

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
     * Db driver implementation for: UserChName
     * 
     * @param string $username
     * @param string $fullname
     * @return Status
     */
    private static function Db_UserChName(
            string $username,
            string $fullname): Status {

        try {
            $query = (new Query(Statement::UPDATE))
                    ->Table(self::$db_users)
                    ->Columns("fullname")
                    ->Values($fullname)
                    ->Where("username", Operator::Eq, $username);
            self::$db_conn->Query($query);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }

    /**
     * Db driver implementation for: UserChPass
     * 
     * @param string $username
     * @param string $password
     * @return Status
     */
    private static function Db_UserChPass(
            string $username,
            string $password): Status {

        try {
            $query = (new Query(Statement::UPDATE))
                    ->Table(self::$db_users)
                    ->Columns("password")
                    ->Values(password_hash($password, PASSWORD_DEFAULT))
                    ->Where("username", Operator::Eq, $username);
            self::$db_conn->Query($query);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
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
     * Db driver implementation for: UserFetch
     * 
     * @param string|null $group
     * @return array
     */
    private static function Db_UserFetch(?string $group): array {
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
     * Db driver implementation for: UserInfo
     * 
     * @param string $username
     * @return array
     */
    private static function Db_UserInfo(string $username): array {
        $query = (new Query(Statement::SELECT))
                ->Table(self::$db_users)
                ->Columns("username", "fullname")
                ->Where("username", Operator::Eq, $username);
        $result = self::$db_conn->Query($query)->fetchAll();

        if (count($result) == 1) {
            // TODO: implement key with all groups of user
            return [
                "fullname" => $result[0]["fullname"],
                "username" => $result[0]["username"]
            ];
        } else {

            return [];
        }
    }

    /**
     * Db driver implementation for: UserInGroup
     * 
     * @param string $username
     * @param string $groupname
     * @return bool
     */
    private static function Db_UserInGroup(
            string $username,
            string $groupname): bool {
        
        $query = (new Query(Statement::SELECT))
                ->Table(self::$db_memberships)
                ->Where("username", Operator::Eq, $username)
                ->And("groupname", Operator::Eq, $groupname);
        $result = self::$db_conn->Query($query)->fetchAll();

        return (count($result) == 1);
    }

    private static function Db_UserJoin(
            string $username,
            string $groupname): Status {
        
        try {
            $query = (new Query(Statement::INSERT))
                    ->Table(self::$db_memberships)
                    ->Columns("username", "groupname")
                    ->Values($username, $groupname);
            self::$db_conn->Query($query);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }

    private static function Db_UserLeave(
            string $username,
            string $groupname): Status {

        try {
            $query = (new Query(Statement::DELETE))
                    ->Table(self::$db_memberships)
                    ->Where("username", Operator::Eq, $username)
                    ->And("groupname", Operator::Eq, $groupname);
            self::$db_conn->Query($query);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }
}
