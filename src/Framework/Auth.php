<?php

/*
 * CWF-PHP Framework
 * 
 * File: Framework\Auth.php
 * Description: Auth API - main class
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework;

use Framework\Config;

class Auth {

    use Auth\Driver_Database; // import drivers
    use Auth\Group,
        Auth\User; // import group and user methods

    /**
     * 
     * @var Auth\Driver Authentication driver (like Db, Ldap)
     */
    private static Auth\Driver $driver;

    /**
     * 
     * @var array `CFGDIR/authentication.json` data
     */
    private static array $auth_cfg;

    /**
     * 
     * @var bool When Auth is properly initialized, the value is true
     */
    private static bool $is_init = false;

    /**
     * Loads configuration stored in `CFGDIR/authentication.json`, if file is
     * not present, then does nothing
     * 
     * @return void
     * @throws Exception
     */
    public static function Init(): void {
        if (self::$is_init) {
            throw new \Exception("AUTH: Can be initialised only once");
        }
        // if no `CFGDIR/auth.json` file, do nothing
        if (!Config::Exists("authentication")) {

            return;
        }

        self::$auth_cfg = Config::Fetch("authentication");
        $driver = \strtolower(self::$auth_cfg["driver"]);

        switch ($driver) {
            case "database":
                self::$driver = Auth\Driver::Database;
                break;
            case "ldap":
                //self::$driver = Auth_Driver::LDAP;
                throw new \Exception("AUTH: LDAP driver is not implemented");
            default:
                throw new \Exception("AUTH: Unknown driver");
        }

        self::$is_init = true;
        self::CallDriver("Setup");
    }

    /**
     * Check, if user is logged in
     * 
     * @return bool
     */
    public static function IsLogged(): bool {
        return isset(
                $_SESSION["_AUTH"]["fullname"],
                $_SESSION["_AUTH"]["login_time"],
                $_SESSION["_AUTH"]["username"]
        );
    }

    /**
     * Authenticate user and setup session
     * 
     * @param string $username
     * @param string $password
     * @return Auth\Status
     */
    public static function Login(
            string $username,
            string $password): Auth\Status {

        if (!self::UserAuth($username, $password)) {

            return Auth\Status::FAILED;
        }

        $user_info = self::UserInfo($username);
        $_SESSION["_AUTH"]["fullname"] = $user_info["fullname"];
        $_SESSION["_AUTH"]["login_time"] = time();
        $_SESSION["_AUTH"]["username"] = $user_info["username"];

        return Auth\Status::SUCCESS;
    }

    /**
     * Logout user
     * 
     * @return void
     */
    public static function Logout(): void {

        unset($_SESSION["_AUTH"]);
    }

    /**
     * Return the array of auth session parameters. When user is not logged in,
     * return an empty array
     * 
     * @return array
     */
    public static function Session(): array {
        if (!self::IsLogged()) {

            return [];
        } else {

            return $_SESSION["_AUTH"];
        }
    }

    /**
     * Call the method of specified driver
     * 
     * @param string $function
     * @param type $params
     * @return mixed
     * @throws Exception
     */
    private static function CallDriver(string $function, ...$params): mixed {
        if (!self::$is_init) {
            throw new \Exception("AUTH: Not initialised");
        }
        // set method name with driver's prefix
        $method = self::$driver->value . "_" . $function;

        if (!\method_exists(self::class, $method)) {
            throw new \Exception("AUTH: Undefined function '{$function}'");
        }

        return self::{$method}(...$params);
    }
}
