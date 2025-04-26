<?php

/*
 * CWF-PHP Framework
 * 
 * File: Auth\Drivers\Json.php
 * Description: Auth API - Json driver
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp\Auth\Drivers;

use CwfPhp\CwfPhp\Auth\Status;
use CwfPhp\CwfPhp\Data;
use CwfPhp\CwfPhp\Interfaces\Auth\Driver as IDriver;

final class Json implements IDriver {

    private string $groups;
    private string $users;
    
    #[\Override]
    public function __construct(array $auth_config) {
        $this->groups = $auth_config["groups_file"] ?? "groups";
        $this->users = $auth_config["users_file"] ?? "users";
    }
    
    #[\Override]
    public function GroupAdd(string $groupname, string $description): Status {
        try {
            Data::Json($this->groups)
                    ->Set($groupname, [
                        "description" => $description,
                        "members" => []
            ]);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }
    
    #[\Override]
    public function GroupChDesc(string $groupname, string $description): Status {
        try {
            $record = Data::Json($this->groups)
                    ->Get($groupname);
            $record["description"] = $description;

            Data::Json($this->groups)
                    ->Set($groupname, $record);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }
    
    #[\Override]
    public function GroupDel(string $groupname): Status {
        try {
            $members = Data::Json($this->groups)->Get($groupname)["members"];

            foreach ($members as $username) {
                $this->UserLeave($username, $groupname);
            }

            Data::Json($this->groups)->Unset($groupname);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }
    
    #[\Override]
    public function GroupExists(string $groupname): bool {
        try {
            Data::Json($this->groups)->Get($groupname);
        } catch (\Throwable) {

            return false;
        }

        return true;
    }
    
    #[\Override]
    public function GroupFetch(): array {
        $result = [];

        try {
            $record = Data::Json($this->groups)->Fetch();

            foreach ($record as $groupname => $details) {
                $result[$groupname] = $details["description"];
            }

            return $result;
        } catch (\Throwable) {

            return [];
        }
    }
    
    #[\Override]
    public function UserAdd(string $username, string $fullname, string $password): Status {
        try {
            Data::Json($this->users)
                    ->Set($username, [
                        "fullname" => $fullname,
                        "groups" => [],
                        "password" => \password_hash($password, \PASSWORD_DEFAULT)
            ]);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }
    
    #[\Override]
    public function UserAuth(string $username, string $password): bool {
        $record = Data::Json($this->users)->Get($username);

        return password_verify($password, $record["password"]);
    }
    
    #[\Override]
    public function UserChName(string $username, string $fullname): Status {
        try {
            $record = Data::Json($this->users)->Get($username);
            $record["fullname"] = $fullname;

            Data::Json($this->users)->Set($username, $record);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }
    
    #[\Override]
    public function UserChPass(string $username,
            string $old_password,
            string $new_password): Status {

        try {
            $record = Data::Json($this->users)->Get($username);
            $record["password"] = password_hash($new_password, \PASSWORD_DEFAULT);

            Data::Json($this->users)->Set($username, $record);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }
    
    #[\Override]
    public function UserDel(string $username): Status {
        try {
            $record = Data::Json($this->users)->Get($username);

            foreach ($record["groups"] as $groupname) {
                $this->UserLeave($username, $groupname);
            }

            Data::Json($this->users)->Unset($username);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }
    
    #[\Override]
    public function UserExists(string $username): bool {
        try {
            Data::Json($this->users)->Get($username);

            return true;
        } catch (\Throwable) {

            return false;
        }
    }
    
    #[\Override]
    public function UserFetch(?string $groupname): array {
        $result = [];

        try {
            if (!\is_null($groupname)) {
                $usr_data = Data::Json($this->groups)->Get($groupname);
            } else {
                $usr_data = Data::Json($this->users)->Fetch();
            }
        } catch (\Throwable) {

            return $result;
        }

        if (!\is_null($groupname)) {
            foreach ($usr_data["members"] as $username) {
                $result[$username] = $this->UserInfo($username)["fullname"];
            }
        } else {
            foreach ($usr_data as $username => $details) {
                $result[$username] = $details["fullname"];
            }
        }

        return $result;
    }
    
    #[\Override]
    public function UserInGroup(string $username, string $groupname): bool {
        try {
            $record = Data::Json($this->users)->Get($username);
        } catch (\Throwable) {

            return false;
        }

        return \in_array($groupname, $record["groups"]);
    }
    
    #[\Override]
    public function UserInfo(string $username): array {
        try {
            $record = Data::Json($this->users)->Get($username);

            return [
                "fullname" => $record["fullname"],
                "username" => $username,
                "groups" => $record["groups"]
            ];
        } catch (\Throwable) {

            return [];
        }
    }
    
    #[\Override]
    public function UserJoin(string $username, string $groupname): Status {
        try {
            $usr_record = Data::Json($this->users)->Get($username);
            $grp_record = Data::Json($this->groups)->Get($groupname);

            \array_push($usr_record["groups"], $groupname);
            \array_push($grp_record["members"], $username);
            Data::Json($this->groups)->Set($groupname, $grp_record);
            Data::Json($this->users)->Set($username, $usr_record);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }
    
    #[\Override]
    public function UserLeave(string $username, string $groupname): Status {
        try {
            $usr_record = Data::Json($this->users)->Get($username);
            $grp_record = Data::Json($this->groups)->Get($groupname);
            $grp_key = \array_search($username, $grp_record["members"]);
            $usr_key = \array_search($groupname, $usr_record["groups"]);

            \array_splice($grp_record["members"], $grp_key, 1);
            \array_splice($usr_record["groups"], $usr_key, 1);
            Data::Json($this->groups)->Set($groupname, $grp_record);
            Data::Json($this->users)->Set($username, $usr_record);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }
}
