<?php

/*
 * CWF-PHP Framework
 * 
 * File: Framework\Auth\Drivers\Json.php
 * Description: Auth API - Json driver
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Auth\Drivers;

use Framework\Auth\Status;
use Framework\Data\Json as JsonFile;

final class Json implements \Framework\Interfaces\Auth_Driver {

    /**
     * 
     * @var string JSON file for groups table
     */
    private string $groups;

    /**
     * 
     * @var string JSON file for users table
     */
    private string $users;

    /**
     * Setup JSON driver environment
     * 
     * @param array $auth_config
     * @return void
     */
    #[\Override]
    public function __construct(array $auth_config) {
        $this->groups = $auth_config["groups_file"] ?? "groups";
        $this->users = $auth_config["users_file"] ?? "users";
    }

    /**
     * Json driver implementation for: GroupAdd
     * 
     * @param string $groupname
     * @param string $description
     * @return Status
     */
    #[\Override]
    public function GroupAdd(string $groupname, string $description): Status {
        try {
            JsonFile::Set($this->groups, $groupname, [
                "description" => $description,
                "members" => []
            ]);
            JsonFile::Update($this->groups);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }

    /**
     * Json driver implementation for: GroupChDesc
     * 
     * @param string $groupname
     * @param string $description
     * @return Status
     */
    #[\Override]
    public function GroupChDesc(string $groupname, string $description): Status {
        try {
            $record = JsonFile::Get($this->groups, $groupname);
            $record["description"] = $description;

            JsonFile::Update($this->groups);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }

    /**
     * Json driver implementation for: GroupDel
     * 
     * @param string $groupname
     * @return Status
     */
    #[\Override]
    public function GroupDel(string $groupname): Status {
        try {
            JsonFile::Unset($this->groups, $groupname);
            JsonFile::Update($this->groups);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }

    /**
     * Json driver implementation for: GroupExists
     * 
     * @param string $groupname
     * @return bool
     */
    #[\Override]
    public function GroupExists(string $groupname): bool {
        try {
            JsonFile::Get($this->groups, $groupname);
        } catch (\Throwable) {

            return false;
        }

        return true;
    }

    /**
     * Json driver implementation for: GroupFetch
     * 
     * @return array
     */
    #[\Override]
    public function GroupFetch(): array {
        try {
            $record = JsonFile::Fetch($this->groups);

            foreach ($record as $groupname => $details) {
                $result[$groupname] = $details["description"];
            }
        } catch (\Throwable) {

            return [];
        }

        return $result;
    }

    /**
     * Json driver implementation for: UserAdd
     * 
     * @param string $username
     * @param string $fullname
     * @param string $password
     * @return Status
     */
    #[\Override]
    public function UserAdd(string $username, string $fullname, string $password): Status {
        try {
            JsonFile::Set($this->users, $username, [
                "fullname" => $fullname,
                "groups" => [],
                "password" => \password_hash($password, \PASSWORD_DEFAULT)
            ]);
            JsonFile::Update($this->users);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }

    /**
     * Json driver implementation for: UserAuth
     * 
     * @param string $username
     * @param string $password
     * @return bool
     */
    #[\Override]
    public function UserAuth(string $username, string $password): bool {
        if (!$this->UserExists($username)) {

            return false;
        }

        $record = JsonFile::Get($this->users, $username);

        return password_verify($password, $record["password"]);
    }

    /**
     * Json driver implementation for: UserChName
     * 
     * @param string $username
     * @param string $fullname
     * @return Status
     */
    #[\Override]
    public function UserChName(string $username, string $fullname): Status {
        try {
            $record = JsonFile::Get($this->users, $username);
            $record["fullname"] = $fullname;

            JsonFile::Set($this->users, $username, $record);
            JsonFile::Update($this->users);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }

    /**
     * Json driver implementation for: UserChPass
     * 
     * @param string $username
     * @param string $password
     * @return Status
     */
    #[\Override]
    public function UserChPass(string $username, string $password): Status {
        try {
            $record = JsonFile::Get($this->users, $username);
            $record["password"] = password_hash($password, \PASSWORD_DEFAULT);

            JsonFile::Set($this->users, $username, $record);
            JsonFile::Update($this->users);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }

    /**
     * Json driver implementation for: UserDel
     * 
     * @param string $username
     * @return Status
     */
    #[\Override]
    public function UserDel(string $username): Status {
        try {
            $record = JsonFile::Get($this->users, $username);
            // remove users from its groups
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }

    /**
     * Json driver implementation for: UserExists
     * 
     * @param string $username
     * @return bool
     */
    #[\Override]
    public function UserExists(string $username): bool {
        try {
            JsonFile::Get($this->users, $username);
        } catch (\Throwable) {

            return false;
        }

        return true;
    }

    /**
     * Json driver implementation for: UserFetch
     * 
     * @param string|null $groupname
     * @return array
     */
    #[\Override]
    public function UserFetch(?string $groupname): array {
        $result = [];

        if (!\is_null($groupname)) {
            foreach (JsonFile::Get($this->groups, $groupname)["members"] as $member) {
                $result[$member] = $this->UserInfo($member)["fullname"];
            }
        } else {
            foreach (JsonFile::Fetch($this->users) as $username => $details) {
                $result[$username] = $details["fullname"];
            }
        }

        return $result;
    }

    /**
     * Json driver implementation for: UserInGroup
     * 
     * @param string $username
     * @param string $groupname
     * @return bool
     */
    #[\Override]
    public function UserInGroup(string $username, string $groupname): bool {
        try {
            $record = JsonFile::Get($this->users, $username);
        } catch (\Throwable) {
            return false;
        }

        return \in_array($groupname, $record["groups"]);
    }

    /**
     * Json driver implementation for: UserInfo
     * 
     * @param string $username
     * @return array|null
     */
    #[\Override]
    public function UserInfo(string $username): ?array {
        try {
            $record = JsonFile::Get($this->users, $username);

            return [
                "username" => $username,
                "fullname" => $record["fullname"]
            ];
        } catch (\Throwable) {

            return [];
        }
    }

    /**
     * Json driver implementation for: UserJoin
     * 
     * @param string $username
     * @param string $groupname
     * @return Status
     */
    #[\Override]
    public function UserJoin(string $username, string $groupname): Status {
        try {
            $usr_record = JsonFile::Get($this->users, $username);
            $grp_record = JsonFile::Get($this->groups, $groupname);
            \array_push($usr_record["groups"], $groupname);
            \array_push($grp_record["members"], $username);

            JsonFile::Set($this->groups, $groupname, $grp_record);
            JsonFile::Set($this->users, $username, $usr_record);
            JsonFile::Update($this->groups);
            JsonFile::Update($this->users);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }

    /**
     * Json driver implementation for: UserLeave
     * 
     * @param string $username
     * @param string $groupname
     * @return Status
     */
    #[\Override]
    public function UserLeave(string $username, string $groupname): Status {
        try {
            $usr_record = JsonFile::Get($this->users, $username);
            $grp_record = JsonFile::Get($this->groups, $groupname);
            $grp_key = array_search($username, $grp_record["members"]);
            $usr_key = array_search($groupname, $usr_record["groups"]);

            unset($grp_record["members"][$grp_key]);
            unset($usr_record["groups"][$usr_key]);
            JsonFile::Set($this->groups, $groupname, $grp_record);
            JsonFile::Set($this->users, $username, $usr_record);
            JsonFile::Update($this->groups);
            JsonFile::Update($this->users);
        } catch (\Throwable) {

            return Status::FAILED;
        }

        return Status::SUCCESS;
    }
}
