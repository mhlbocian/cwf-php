<?php

/*
 * CWF-PHP Framework
 * 
 * File: Framework\Auth\Auth.php
 * Description: Auth API - user methods
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Auth;

trait User {

    /**
     * Add user
     * 
     * @param string $username
     * @param string $fullname
     * @param string $password
     * @return Status
     */
    #[\Override]
    public static function UserAdd(
            string $username,
            string $fullname,
            string $password): Status {

        if (self::UserExists($username)) {

            return Status::EXISTS;
        }
        /**
         * @TODO check full name format requirements
         */
        return self::CallDriver("UserAdd", $username, $fullname, $password);
    }

    /**
     * Check, if given credentials are correct
     * 
     * @param string $login
     * @param string $password
     * @return bool
     */
    #[\Override]
    public static function UserAuth(string $username, string $password): bool {

        return self::CallDriver("UserAuth", $username, $password);
    }

    /**
     * Change full name of user
     * 
     * @param string $username
     * @param string $fullname
     * @return Status
     */
    #[\Override]
    public static function UserChName(
            string $username,
            string $fullname): Status {

        if (!self::UserExists($username)) {
            /** @TODO Check full name format requirements */
            return Status::INVALID_INPUT;
        }

        return self::CallDriver("UserChName", $username, $fullname);
    }

    /**
     * Change password of user
     * 
     * @param string $username
     * @param string $old_password
     * @param string $new_password
     * @return Status
     */
    #[\Override]
    public static function UserChPass(
            string $username,
            string $old_password,
            string $new_password): Status {

        if (!self::UserAuth($username, $old_password)) {

            return Status::INVALID_INPUT;
        }

        return self::CallDriver("UserChPass", $username, $new_password);
    }

    /**
     * Delete user
     * 
     * @param string $username
     * @return Status
     */
    #[\Override]
    public static function UserDel(string $username): Status {
        if (!self::UserExists($username)) {
            return Status::INVALID_INPUT;
        }

        return self::CallDriver("UserDel", $username);
    }

    /**
     * Check, if user exists for given login
     * 
     * @param string $login
     * @return bool
     */
    #[\Override]
    public static function UserExists(string $username): bool {

        return self::CallDriver("UserExists", $username);
    }

    /**
     * Return an array of users
     * 
     * @param string|null $groupname
     * @return array ["username1"=>"fullname1", ...]
     */
    #[\Override]
    public static function UserFetch(?string $groupname = null): array {

        return self::CallDriver("UserFetch", $groupname);
    }

    /**
     * Return an array with user information. When user does not exist, return
     * an empty array.
     * 
     * @param string $username
     * @return ?array null, when user does not exist
     */
    #[\Override]
    public static function UserInfo(string $username): ?array {

        return self::CallDriver("UserInfo", $username);
    }

    /**
     * Check, if user belong to specified group
     * 
     * @param string $username
     * @param string $groupname
     * @return bool
     */
    #[\Override]
    public static function UserInGroup(
            string $username,
            string $groupname): bool {

        return self::CallDriver("UserInGroup", $username, $groupname);
    }

    /**
     * Join user to group
     * 
     * @param string $username
     * @param string $groupname
     * @return Status
     */
    #[\Override]
    public static function UserJoin(
            string $username,
            string $groupname): Status {

        if (!self::UserExists($username) || !self::GroupExists($groupname)) {

            return Status::INVALID_INPUT;
        }

        if (self::UserInGroup($username, $groupname)) {

            return Status::EXISTS;
        }

        return self::CallDriver("UserJoin", $username, $groupname);
    }

    /**
     * Remove user from group
     * 
     * @param string $username
     * @param string $groupname
     * @return Status
     */
    #[\Override]
    public static function UserLeave(
            string $username,
            string $groupname): Status {

        if (!self::UserInGroup($username, $groupname)) {

            return Status::INVALID_INPUT;
        }

        return self::CallDriver("UserLeave", $username, $groupname);
    }
}
