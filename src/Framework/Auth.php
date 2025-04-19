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

use Framework\Auth\Status;
use Framework\Config;

final class Auth implements Interfaces\Auth {

    use Auth\Group,
        Auth\User; // import group and user methods

    private const string DRIVERS_NAMESPACE = __NAMESPACE__ . "\\Auth\\Drivers";

    /**
     * 
     * @var Auth\Driver Authentication driver (like Db, Ldap)
     */
    private static Interfaces\Auth_Driver $driver;

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
     * Call the function of the driver
     * 
     * @param string $function
     * @param type $params
     * @return mixed
     * @throws Exception
     */
    #[\Override]
    public static function CallDriver(
            string $function,
            mixed ...$params): mixed {

        if (!self::$is_init) {
            throw new \Exception("AUTH: Not initialised");
        }

        if (!\method_exists(self::$driver, $function)) {
            throw new \Exception("AUTH: Undefined function '{$function}'");
        }

        try {
            $ret = self::$driver->{$function}(...$params);
        } catch (\Throwable) {

            throw new \Exception("AUTH: Error occured in '{$function}'");
        }

        return $ret;
    }

    /**
     * Loads configuration stored in `CFGDIR/authentication.json`, if file is
     * not present, then does nothing
     * 
     * @return void
     * @throws Exception
     */
    #[\Override]
    public static function Init(): void {
        if (self::$is_init) {
            throw new \Exception("AUTH: Can be initialised only once");
        }
        // if no `CFGDIR/auth.json` file, do nothing
        if (!Config::Exists("authentication")) {

            return;
        }

        self::$auth_cfg = Config::Fetch("authentication");

        // format driver name as first letter uppercase, the rest lowercase
        self::InitDriver(\ucfirst(\strtolower(self::$auth_cfg["driver"])));

        self::$is_init = true;
    }

    /**
     * Check, if user is logged in
     * 
     * @return bool
     */
    #[\Override]
    public static function IsLogged(): bool {
        /**
         * @TODO now its insecure against session hijacking
         */
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
    #[\Override]
    public static function Login(
            string $username,
            string $password): Status {

        if (!self::UserAuth($username, $password)) {

            return Status::FAILED;
        }

        $user_info = self::UserInfo($username);
        $_SESSION["_AUTH"]["fullname"] = $user_info["fullname"];
        $_SESSION["_AUTH"]["login_time"] = time();
        $_SESSION["_AUTH"]["username"] = $user_info["username"];
        /**
         * @TODO now its insecure against session hijacking
         */
        return Status::SUCCESS;
    }

    /**
     * Logout user
     * 
     * @return void
     */
    #[\Override]
    public static function Logout(): void {

        unset($_SESSION["_AUTH"]);
    }

    /**
     * Return the array of auth session parameters. When user is not logged in,
     * return an empty array
     * 
     * @return array
     */
    #[\Override]
    public static function Session(): array {
        if (!self::IsLogged()) {

            return [];
        } else {

            return $_SESSION["_AUTH"];
        }
    }

    /**
     * Check if class for given driver exists, and is an implementation of the
     * `Framework\Interfaces\Auth_Driver` interface
     * 
     * @param string $driver Driver name
     * @return void
     * @throws \Exception
     */
    private static function InitDriver(string $driver): void {
        $class_fqn = self::DRIVERS_NAMESPACE . "\\" . $driver;

        if (!class_exists($class_fqn)) {

            throw new \Exception("AUTH: Unknown driver '{$driver}'");
        }

        try {
            self::$driver = new $class_fqn(self::$auth_cfg);
        } catch (\TypeError) {
            // usually when driver class not implements `Auth_Driver`
            throw new \Exception("AUTH: '{$class_fqn}' is not a vaild  driver");
        } catch (\Throwable) {
            // when initiation error occurs inside driver
            throw new \Exception("AUTH: Driver '{$driver}' init error");
        }
    }
}
