<?php

/*
 * CWF-PHP Framework
 * 
 * File: Framework\Interfaces\Auth.php
 * Description: Auth API - interface
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Interfaces;

use Framework\Auth\Status;

interface Auth {

    public static function CallDriver(
            string $function,
            mixed ...$params): mixed;

    public static function GroupAdd(
            string $groupname,
            string $description): Status;

    public static function GroupChDesc(
            string $groupname,
            string $description): Status;

    public static function GroupDel(string $groupname): Status;

    public static function GroupExists(string $groupname): bool;

    public static function GroupFetch(): array;

    public static function Init(): void;

    public static function IsLogged(): bool;

    public static function Login(
            string $username,
            string $password): Status;

    public static function Logout(): void;

    public static function Session(): array;

    public static function UserAdd(
            string $username,
            string $fullname,
            string $password): Status;

    public static function UserAuth(
            string $username,
            string $password): bool;

    public static function UserChName(
            string $username,
            string $fullname): Status;

    public static function UserChPass(
            string $username,
            string $old_password,
            string $new_password): Status;

    public static function UserDel(string $username): Status;

    public static function UserExists(string $username): bool;

    public static function UserFetch(?string $groupname = null): array;

    public static function UserInfo(string $username): ?array;

    public static function UserInGroup(
            string $username,
            string $groupname): bool;

    public static function UserJoin(
            string $username,
            string $groupname): Status;

    public static function UserLeave(
            string $username,
            string $groupname): Status;
}
