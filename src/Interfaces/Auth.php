<?php

/*
 * CWF-PHP Framework
 * 
 * File: Interfaces\Auth.php
 * Description: Auth API - interface
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp\Interfaces;

use CwfPhp\CwfPhp\Auth\Status;

interface Auth extends Auth\Common {

    public static function Instance(): Auth;

    public function IsLogged(): bool;

    public function Login(
            string $username,
            string $password): Status;

    public function Logout(): void;

    public function Session(): array;
}
