<?php

/*
 * CWF-PHP Framework
 * 
 * File: Framework\Interfaces\Auth_Driver.php
 * Description: Auth API - driver interface
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Interfaces;

use Framework\Auth\Status;

interface Auth_Driver {

    public function __construct(array $auth_config);

    public function GroupAdd(
            string $groupname,
            string $description): Status;

    public function GroupChDesc(
            string $groupname,
            string $description): Status;

    public function GroupDel(
            string $groupname): Status;

    public function GroupExists(
            string $groupname): bool;

    public function GroupFetch(): array;

    public function UserAdd(
            string $username,
            string $fullname,
            #[\SensitiveParameter] string $password): Status;

    public function UserAuth(
            string $username,
            #[\SensitiveParameter] string $password): bool;

    public function UserChName(
            string $username,
            string $fullname): Status;

    public function UserChPass(
            string $username,
            #[\SensitiveParameter] string $password): Status;
    
    public function UserDel(string $username): Status;

    public function UserExists(
            string $username): bool;

    public function UserFetch(
            ?string $group): array;

    public function UserInfo(
            string $username): ?array;

    public function UserInGroup(
            string $username,
            string $groupname): bool;

    public function UserJoin(
            string $username,
            string $groupname): Status;

    public function UserLeave(
            string $username,
            string $groupname): Status;
}
