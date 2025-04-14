<?php

/*
 * CWF-PHP Framework
 * 
 * File: Auth.php
 * Description: Authentication framework
 * Author: Michał Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework;

use Exception;
use Framework\Config;

class Auth {

    // import drivers
    use Auth\Driver_Db;

    /**
     * 
     * @var Auth\Driver Authentication driver (like Db, Ldap)
     */
    private static Auth\Driver $driver;
    
    /**
     * 
     * @var array CFGDIR/authentication.json data
     */
    private static array $auth_cfg;
    
    /**
     * 
     * @var bool When Auth is properly initialized, the value is true
     */
    private static bool $is_init = false;

    /**
     * Loads configuration stored in CFGDIR/authentication.json, if file is not
     * present, then does nothing
     * 
     * @return void
     * @throws Exception
     */
    public static function Init(): void {
        if (self::$is_init) {
            throw new Exception("AUTH: Can be initialised only once");
        }
        // if no CFGDIR/auth.json file, do nothing
        if (!Config::Exists("authentication")) {

            return;
        }

        self::$auth_cfg = Config::Fetch("authentication");
        $driver = strtolower(self::$auth_cfg["driver"]);

        switch ($driver) {
            case "database":
                self::$driver = Auth\Driver::Database;
                break;
            case "ldap":
                //self::$driver = Auth_Driver::LDAP;
                throw new Exception("AUTH: LDAP driver is not implemented");
            default:
                throw new Exception("AUTH: Unknown driver");
        }

        self::$is_init = true;
        self::Call_Driver("Setup");
    }

    /**
     * Check, if given credentials are correct
     * 
     * @param string $login
     * @param string $password
     * @return bool
     */
    public static function AuthUser(string $username, string $password): bool {

        return self::Call_Driver("AuthUser", $username, $password);
    }

    /**
     * Return an array of groups
     * 
     * @return array ["groupname1"=>"description1", ...]
     */
    public static function GetGroups(): array {

        return self::Call_Driver("GetGroups");
    }

    /**
     * Return an array of users
     * 
     * @param string|null $groupname
     * @return array ["username1"=>"fullname1", ...]
     */
    public static function GetUsers(?string $groupname = null): array {

        return self::Call_Driver("GetUsers", $groupname);
    }

    /**
     * Check, if group exists for given name
     * 
     * @param string $groupname
     * @return bool
     */
    public static function GroupExists(string $groupname): bool {

        return self::Call_Driver("GroupExists", $groupname);
    }

    /**
     * Check, if user exists for given login
     * 
     * @param string $login
     * @return bool
     */
    public static function UserExists(string $username): bool {

        return self::Call_Driver("UserExists", $username);
    }

    /**
     * Check, if user belong to specified group
     * 
     * @param string $username
     * @param string $groupname
     * @return bool
     */
    public static function UserInGroup(string $username, string $groupname): bool {

        return self::Call_Driver("UserInGroup", $username, $groupname);
    }

    /**
     * Call the method of specified driver
     * 
     * @param string $function
     * @param type $params
     * @return mixed
     * @throws Exception
     */
    private static function Call_Driver(string $function, ...$params): mixed {
        if (!self::$is_init) {
            throw new Exception("AUTH: Not initialised");
        }
        // set method name with driver's prefix
        $method = self::$driver->value . "_" . $function;

        if (!method_exists(self::class, $method)) {
            throw new Exception("AUTH: Undefined function '{$function}'");
        }

        return self::{$method}(...$params);
    }
}
