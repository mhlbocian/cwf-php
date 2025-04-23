<?php

/*
 * CWF-PHP Framework
 * 
 * File: Framework\Drivers\Database.php
 * Description: Auth API - Database driver
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Auth\Drivers;

use Framework\Auth\Status;
use Framework\Database as Db;
use Framework\Interfaces\Auth\Driver as IDriver;
use Framework\Query;
use Framework\Query\{
    Operator,
    Statement
};

final class Database implements IDriver {
    
    private Db $conn;
    private string $conn_name;
    private string $grp_table;
    private string $mbr_table;
    private string $usr_table;
    
    #[\Override]
    public function __construct(array $auth_config) {
        if (!isset($auth_config["connection"])) {

            throw new \Exception("no connection specified");
        }
        // connection name is mandatory
        $this->conn_name = $auth_config["connection"];
        // if the rest of keys are not in config file, load default values
        $this->grp_table = $auth_config["groups_table"] ?? "groups";
        $this->mbr_table = $auth_config["memberships_table"] ?? "memberships";
        $this->usr_table = $auth_config["users_table"] ?? "users";

        try {
            $this->conn = new Db($this->conn_name);
        } catch (\Throwable) {

            throw new \Exception("connection error");
        }
    }
    
    #[\Override]
    public function GroupAdd(
            string $groupname,
            string $description): Status {

        $query = new Query(Statement::INSERT)
                ->Table($this->grp_table)
                ->Columns("groupname", "description")
                ->Values($groupname)
                ->Values($description);

        try {
            $this->conn->Query($query);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }
    
    #[\Override]
    public function GroupChDesc(
            string $groupname,
            string $description): Status {

        $query = new Query(Statement::UPDATE)
                ->Table($this->grp_table)
                ->Columns("groupname", "description")
                ->Values($groupname, $description)
                ->Where("groupname", Operator::Eq, $groupname);

        try {
            $this->conn->Query($query);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }
    
    #[\Override]
    public function GroupDel(string $groupname): Status {
        $query_mbr = new Query(Statement::DELETE)
                ->Table($this->mbr_table)
                ->Where("groupname", Operator::Eq, $groupname);
        $query_grp = (new Query(Statement::DELETE))
                ->Table($this->grp_table)
                ->Where("groupname", Operator::Eq, $groupname);

        try {
            $this->conn->Query($query_mbr);
            $this->conn->Query($query_grp);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }
    
    #[\Override]
    public function GroupExists(string $groupname): bool {
        $query = new Query(Statement::SELECT)
                ->Table($this->grp_table)
                ->Where("groupname", Operator::Eq, $groupname);

        try {
            $result = $this->conn->Query($query)->fetchAll();
        } catch (\Throwable) {

            return false;
        }

        return (count($result) == 1);
    }
    
    #[\Override]
    public function GroupFetch(): array {
        $output = [];
        $query = new Query(Statement::SELECT)
                ->Table($this->grp_table);

        try {
            $result = $this->conn->Query($query);
        } catch (\Throwable) {
            return $output;
        }

        foreach ($result as $row) {
            $output[$row["groupname"]] = $row["description"];
        }

        return $output;
    }
    
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
    
    #[\Override]
    public function UserAuth(
            string $username,
            string $password): bool {

        $query = new Query(Statement::SELECT)
                ->Table($this->usr_table)
                ->Where("username", Operator::Eq, $username);

        try {
            $result = $this->conn->Query($query)->fetchAll();
        } catch (\Throwable) {

            return false;
        }

        if (count($result) != 1) {

            return false;
        }

        return password_verify($password, $result[0]["password"]);
    }
    
    #[\Override]
    public function UserChName(
            string $username,
            string $fullname): Status {

        $query = new Query(Statement::UPDATE)
                ->Table($this->usr_table)
                ->Columns("fullname")
                ->Values($fullname)
                ->Where("username", Operator::Eq, $username);

        try {
            $this->conn->Query($query);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }
    
    #[\Override]
    public function UserChPass(
            string $username,
            string $old_password,
            string $new_password): Status {

        $query = new Query(Statement::UPDATE)
                ->Table($this->usr_table)
                ->Columns("password")
                ->Values(password_hash($new_password, PASSWORD_DEFAULT))
                ->Where("username", Operator::Eq, $username);

        try {
            $this->conn->Query($query);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }
    
    #[\Override]
    public function UserDel(string $username): Status {
        $query_mbr = new Query(Statement::DELETE)
                ->Table($this->mbr_table)
                ->Where("username", Operator::Eq, $username);
        $query_usr = new Query(Statement::DELETE)
                ->Table($this->usr_table)
                ->Where("username", Operator::Eq, $username);

        try {
            $this->conn->Query($query_mbr);
            $this->conn->Query($query_usr);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }
    
    #[\Override]
    public function UserExists(string $username): bool {
        $query = new Query(Statement::SELECT)
                ->Table($this->usr_table)
                ->Where("username", Operator::Eq, $username);

        try {
            $result = $this->conn->Query($query)->fetchAll();
        } catch (Throwable) {

            return false;
        }

        return (count($result) == 1);
    }
    
    #[\Override]
    public function UserFetch(?string $groupname): array {
        $output = [];
        $usr_fncol = "{$this->usr_table}.fullname";
        $usr_usrcol = "{$this->usr_table}.username";
        $query = new Query(Statement::SELECT)
                ->Table($this->usr_table)
                ->Columns($usr_usrcol, $usr_fncol);

        if ($groupname != null) {
            $mbr_grpcol = "{$this->mbr_table}.groupname";
            $mbr_usrcol = "{$this->mbr_table}.username";
            $query->Join($this->mbr_table, $mbr_usrcol, $usr_usrcol)
                    ->Where($mbr_grpcol, Operator::Eq, $groupname);
        }

        try {
            $result = $this->conn->Query($query);
        } catch (\Throwable) {

            return $output;
        }


        foreach ($result as $row) {
            $output[$row["username"]] = $row["fullname"];
        }

        return $output;
    }
    
    #[\Override]
    public function UserInfo(string $username): array {
        $query_usr = new Query(Statement::SELECT)
                ->Table($this->usr_table)
                ->Columns("username", "fullname")
                ->Where("username", Operator::Eq, $username);
        $query_grp = new Query(Statement::SELECT)
                ->Table($this->mbr_table)
                ->Columns("groupname")
                ->Where("username", Operator::Eq, $username);

        try {
            $result = $this->conn->Query($query_usr)->fetchAll();
            $groups = $this->conn->Query($query_grp)->fetchAll();
        } catch (\Throwable) {

            return [];
        }

        if (count($result) == 1) {
            $output = [
                "fullname" => $result[0]["fullname"],
                "username" => $result[0]["username"],
                "groups" => []
            ];

            foreach ($groups as $row) {
                $output["groups"][] = $row["groupname"];
            }

            return $output;
        } else {

            return [];
        }
    }
    
    #[\Override]
    public function UserInGroup(
            string $username,
            string $groupname): bool {

        $query = new Query(Statement::SELECT)
                ->Table($this->mbr_table)
                ->Where("username", Operator::Eq, $username)
                ->And("groupname", Operator::Eq, $groupname);

        try {
            $result = $this->conn->Query($query)->fetchAll();
        } catch (\Throwable) {

            return false;
        }

        return (count($result) == 1);
    }
    
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
    
    #[\Override]
    public function UserLeave(
            string $username,
            string $groupname): Status {

        $query = new Query(Statement::DELETE)
                ->Table($this->mbr_table)
                ->Where("username", Operator::Eq, $username)
                ->And("groupname", Operator::Eq, $groupname);

        try {
            $this->conn->Query($query);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }
}
