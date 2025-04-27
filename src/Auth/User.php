<?php

/*
 * CWF-PHP Framework
 * 
 * File: Auth\User.php
 * Description: Auth API - user methods
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp\Auth;

use CwfPhp\CwfPhp\Auth\Status;

trait User {

    #[\Override]
    public function UserAdd(
            string $username,
            string $fullname,
            string $password): Status {

        if (!$this->configured) {
            
            return Status::FAILED;
        }

        if ($this->UserExists($username)) {

            return Status::EXISTS;
        }

        if (!$this->Format_Check($username, $this->username_fmt) ||
                !$this->Format_Check($fullname, $this->fullname_fmt) ||
                !$this->Format_Check($password, $this->password_fmt)) {

            return Status::INVALID_INPUT;
        }
        // as fullname may contain HTML specific characters, filter it
        $filter_fname = \htmlspecialchars($fullname);

        return $this->driver->UserAdd($username, $filter_fname, $password);
    }

    #[\Override]
    public function UserAuth(string $username, string $password): bool {
        if (!$this->configured || !$this->UserExists($username)) {
            
            return false;
        }

        return $this->driver->UserAuth($username, $password);
    }

    #[\Override]
    public function UserChName(
            string $username,
            string $fullname): Status {

        if (!$this->configured) {
            
            return Status::FAILED;
        }

        if (!$this->UserExists($username) ||
                !$this->Format_Check($fullname, $this->fullname_fmt)) {

            return Status::INVALID_INPUT;
        }
        // as fullname may contain HTML specific characters, filter it
        $filter_fname = \htmlspecialchars($fullname);

        return $this->driver->UserChName($username, $filter_fname);
    }

    #[\Override]
    public function UserChPass(
            string $username,
            string $old_password,
            string $new_password): Status {

        if (!$this->configured) {
            
            return Status::FAILED;
        }

        if (!$this->UserAuth($username, $old_password) ||
                !$this->Format_Check($new_password, $this->password_fmt)) {

            return Status::INVALID_INPUT;
        }

        return $this->driver->UserChPass($username, "", $new_password);
    }

    #[\Override]
    public function UserDel(string $username): Status {
        if (!$this->configured) {
            
            return Status::FAILED;
        }

        if (!$this->UserExists($username)) {
            return Status::NOTEXISTS;
        }

        return $this->driver->UserDel($username);
    }

    #[\Override]
    public function UserExists(string $username): bool {
        if (!$this->configured) {
            
            return false;
        }

        return $this->driver->UserExists($username);
    }

    #[\Override]
    public function UserFetch(?string $groupname = null): array {
        if (!$this->configured ||
                !\is_null($groupname) &&
                !$this->GroupExists($groupname)) {

            return [];
        }

        return $this->driver->UserFetch($groupname);
    }

    #[\Override]
    public function UserInfo(string $username): array {
        if (!$this->configured || !$this->UserExists($username)) {
            
            return [];
        }

        return $this->driver->UserInfo($username);
    }

    #[\Override]
    public function UserInGroup(
            string $username,
            string $groupname): bool {

        if (!$this->configured) {
            
            return false;
        }

        return $this->driver->UserInGroup($username, $groupname);
    }

    #[\Override]
    public function UserJoin(
            string $username,
            string $groupname): Status {

        if (!$this->configured) {
            
            return Status::FAILED;
        }

        if (!$this->UserExists($username) || !$this->GroupExists($groupname)) {

            return Status::INVALID_INPUT;
        }

        if ($this->UserInGroup($username, $groupname)) {

            return Status::EXISTS;
        }

        return $this->driver->UserJoin($username, $groupname);
    }

    #[\Override]
    public function UserLeave(
            string $username,
            string $groupname): Status {

        if (!$this->configured) {
            
            return Status::FAILED;
        }

        if (!$this->UserInGroup($username, $groupname)) {

            return Status::INVALID_INPUT;
        }

        return $this->driver->UserLeave($username, $groupname);
    }
}
