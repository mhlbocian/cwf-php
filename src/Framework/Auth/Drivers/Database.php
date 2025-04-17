<?php

/*
 * CWF-PHP Framework
 * 
 * File: Framework\Auth\Auth.php
 * Description: Auth API - Database driver
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Auth\Drivers;

use Framework\Auth\Status;
use Framework\Query;
use Framework\Query\{
    Operator,
    Statement
};

final class Database implements \Framework\Interfaces\Auth_Driver {

    /**
     * 
     * @var Database Connection handler
     */
    private \Framework\Database $conn;

    /**
     * 
     * @var string Connection name
     */
    private string $conn_name;

    /**
     * 
     * @var string Table name for groups data
     */
    private string $grp_table;

    /**
     * 
     * @var string Table name for memberships data
     */
    private string $mbr_table;

    /**
     * 
     * @var string Table name for users data
     */
    private string $usr_table;

    /**
     * Setup database connection and driver-specific properties
     * 
     * @param array $auth_config
     * @return void
     */
    #[\Override]
    public function __construct(array $auth_config) {
        if (!isset($auth_config["connection"])) {

            throw new \Exception("AUTH-Database: no connection name");
        }
        // connection name is mandatory
        $this->conn_name = $auth_config["connection"];
        // if the rest of keys are not in config file, load default values
        $this->grp_table = $auth_config["groups_table"] ?? "groups";
        $this->mbr_table = $auth_config["memberships_table"] ?? "memberships";
        $this->usr_table = $auth_config["users_table"] ?? "users";
        $this->conn = new \Framework\Database($this->conn_name);
    }

    /**
     * Db driver implementation for: GroupAdd
     * 
     * @param string $groupname
     * @param string $description
     * @return Status
     */
    #[\Override]
    public function GroupAdd(
            string $groupname,
            string $description): Status {

        try {
            $query = new Query(Statement::INSERT)
                    ->Table($this->grp_table)
                    ->Columns("groupname", "description")
                    ->Values($groupname)
                    ->Values($description);
            $this->conn->Query($query);
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
    #[\Override]
    public function GroupChDesc(
            string $groupname,
            string $description): Status {

        try {
            $query = new Query(Statement::UPDATE)
                    ->Table($this->grp_table)
                    ->Columns("groupname", "description")
                    ->Values($groupname, $description)
                    ->Where("groupname", Operator::Eq, $groupname);
            $this->conn->Query($query);
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
    #[\Override]
    public function GroupDel(string $groupname): Status {
        try {
            // remove membership relations
            $query = new Query(Statement::DELETE)
                    ->Table($this->mbr_table)
                    ->Where("groupname", Operator::Eq, $groupname);
            $this->conn->Query($query);
            // remove the group
            $query = (new Query(Statement::DELETE))
                    ->Table($this->grp_table)
                    ->Where("groupname", Operator::Eq, $groupname);
            $this->conn->Query($query);
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
    #[\Override]
    public function GroupExists(string $groupname): bool {
        $query = new Query(Statement::SELECT)
                ->Table($this->grp_table)
                ->Where("groupname", Operator::Eq, $groupname);
        $result = $this->conn->Query($query)->fetchAll();

        return (count($result) == 1);
    }

    /**
     * Db driver implementation for: GroupFetch
     * 
     * @return array
     */
    #[\Override]
    public function GroupFetch(): array {
        $output = [];

        $query = new Query(Statement::SELECT)
                ->Table($this->grp_table);
        $result = $this->conn->Query($query);

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
    #[\Override]
    public function UserAdd(
            string $username,
            string $fullname,
            string $password): Status {

        $query = new Query(Statement::INSERT)
                ->Table($this->usr_table)
                ->Columns("username", "fullname", "password")
                ->Values($username)
                ->Values($fullname)
                ->Values(password_hash($password, PASSWORD_DEFAULT));

        try {
            $this->conn->Query($query);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }

    /**
     * Db driver implementation for: UserAuth
     * 
     * @param string $username
     * @param string $password
     * @return bool
     */
    #[\Override]
    public function UserAuth(
            string $username,
            string $password): bool {

        $query = new Query(Statement::SELECT)
                ->Table($this->usr_table)
                ->Where("username", Operator::Eq, $username);
        $result = $this->conn->Query($query)->fetchAll();

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
    #[\Override]
    public function UserChName(
            string $username,
            string $fullname): Status {

        try {
            $query = new Query(Statement::UPDATE)
                    ->Table($this->usr_table)
                    ->Columns("fullname")
                    ->Values($fullname)
                    ->Where("username", Operator::Eq, $username);
            $this->conn->Query($query);
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
    #[\Override]
    public function UserChPass(
            string $username,
            string $password): Status {

        try {
            $query = new Query(Statement::UPDATE)
                    ->Table($this->usr_table)
                    ->Columns("password")
                    ->Values(password_hash($password, PASSWORD_DEFAULT))
                    ->Where("username", Operator::Eq, $username);
            $this->conn->Query($query);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }

    /**
     * Db driver implementation for: UserExists
     * 
     * @param string $username
     * @return bool
     */
    #[\Override]
    public function UserExists(string $username): bool {
        $query = new Query(Statement::SELECT)
                ->Table($this->usr_table)
                ->Where("username", Operator::Eq, $username);
        $result = $this->conn->Query($query)->fetchAll();

        return (count($result) == 1);
    }

    /**
     * Db driver implementation for: UserFetch
     * 
     * @param string|null $group
     * @return array
     */
    #[\Override]
    public function UserFetch(?string $group): array {
        $output = [];

        if ($group == null) {
            $query = new Query(Statement::SELECT)
                    ->Table($this->usr_table);
        } else {
            // TODO: QUERY JOIN OPERATIONS, now return empty array
            return $output;
        }

        $result = $this->conn->Query($query);

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
    #[\Override]
    public function UserInfo(string $username): array {
        $query = new Query(Statement::SELECT)
                ->Table($this->usr_table)
                ->Columns("username", "fullname")
                ->Where("username", Operator::Eq, $username);
        $result = $this->conn->Query($query)->fetchAll();

        if (count($result) == 1) {
            /**
             * @TODO implement array key for all groups of user
             */
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
    #[\Override]
    public function UserInGroup(
            string $username,
            string $groupname): bool {

        $query = new Query(Statement::SELECT)
                ->Table($this->mbr_table)
                ->Where("username", Operator::Eq, $username)
                ->And("groupname", Operator::Eq, $groupname);
        $result = $this->conn->Query($query)->fetchAll();

        return (count($result) == 1);
    }

    /**
     * Db driver implementation for: UserJoin
     * 
     * @param string $username
     * @param string $groupname
     * @return bool
     */
    #[\Override]
    public function UserJoin(
            string $username,
            string $groupname): Status {

        try {
            $query = new Query(Statement::INSERT)
                    ->Table($this->mbr_table)
                    ->Columns("username", "groupname")
                    ->Values($username, $groupname);
            $this->conn->Query($query);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }

    /**
     * Db driver implementation for: UserLeave
     * 
     * @param string $username
     * @param string $groupname
     * @return bool
     */
    #[\Override]
    public function UserLeave(
            string $username,
            string $groupname): Status {

        try {
            $query = new Query(Statement::DELETE)
                    ->Table($this->mbr_table)
                    ->Where("username", Operator::Eq, $username)
                    ->And("groupname", Operator::Eq, $groupname);
            $this->conn->Query($query);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }
}
