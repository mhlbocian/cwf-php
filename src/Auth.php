<?php

/*
 * CWF-PHP Framework
 * 
 * File: Auth.php
 * Description: Auth API - main class
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp;

use CwfPhp\CwfPhp\Auth\Status;
use CwfPhp\CwfPhp\Config;
use CwfPhp\CwfPhp\Interfaces\Auth\Driver as IDriver;
use CwfPhp\CwfPhp\Interfaces\Auth as IAuth;

final class Auth implements IAuth {

    use Auth\Group,
        Auth\User;

    private const string DRIVERS_NAMESPACE = __NAMESPACE__ . "\\Auth\\Drivers";

    private array $config;
    private bool $configured = false;
    /* driver specific properites */
    private IDriver $driver;
    private static ?Auth $instance = null;
    private ?string $driver_namespace = null;
    /* fields format properties */
    private string $description_fmt = ".{5,}";
    private string $fullname_fmt = ".{5,}";
    private string $groupname_fmt = "[\w][\w.]{4,}";
    private string $password_fmt = ".{8,}";
    private string $username_fmt = "[\w][\w.]{4,}";

    public function __construct() {
        if (!Config::Exists("authentication")) {

            return;
        }

        $this->config = Config::File("authentication")->Fetch();
        // setup required string formats
        $this->Format_LoadConfig();
        // for custom drivers (stored outside CWF-PHP) namespace is required
        $this->driver_namespace = $this->config["namespace"] ?? null;
        // format driver name as first letter uppercase, the rest lowercase
        $this->Driver_Setup(\ucfirst(\strtolower($this->config["driver"])));
    }

    #[\Override]
    public static function Instance(): Auth {
        if (!isset(self::$instance)) {
            // initiate object instance
            self::$instance = new Auth();
        }

        return self::$instance;
    }

    /** Search for internal or external driver and set it up */
    private function Driver_Setup(string $driver): void {
        if ($this->driver_namespace === null) {
            // search for internal driver
            $class_fqn = self::DRIVERS_NAMESPACE . "\\" . $driver;
        } else {
            // search for external driver
            $class_fqn = "{$this->driver_namespace}\\{$driver}";
        }

        if (!\class_exists($class_fqn)) {

            throw new \Exception("AUTH: Unknown driver '{$driver}'");
        }

        try {
            $this->driver = new $class_fqn($this->config);
        } catch (\TypeError) {
            // usually when driver class not implements `Auth_Driver`
            throw new \Exception("AUTH: '{$class_fqn}' is not a vaild  driver");
        } catch (\Throwable) {
            // when initiation error occurs inside driver
            throw new \Exception("AUTH: Driver '{$driver}' init error");
        }

        $this->configured = true;
    }

    /** Check, if the input string format is correct */
    private function Format_Check(string $string, string $fmt): bool {

        return (\preg_match("/^{$fmt}$/", $string) === 1);
    }

    /** Load format requirements from config file, if available */
    private function Format_LoadConfig(): void {
        $fmts = $this->config["format"] ?? null;
        $this->username_fmt = $fmts["username"] ?? $this->username_fmt;
        $this->fullname_fmt = $fmts["fullname"] ?? $this->fullname_fmt;
        $this->password_fmt = $fmts["password"] ?? $this->password_fmt;
        $this->groupname_fmt = $fmts["groupname"] ?? $this->groupname_fmt;
        $this->description_fmt = $fmts["description"] ?? $this->description_fmt;
    }

    #[\Override]
    public function IsLogged(): bool {
        if (!$this->configured) {
            return false;
        }
        /**
         * @TODO now its insecure against session hijacking
         */
        return isset(
                $_SESSION["_AUTH"]["fullname"],
                $_SESSION["_AUTH"]["login_time"],
                $_SESSION["_AUTH"]["username"]
        );
    }

    #[\Override]
    public function Login(
            string $username,
            string $password): Status {

        if (!$this->configured || !self::UserAuth($username, $password)) {

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

    #[\Override]
    public function Logout(): void {

        unset($_SESSION["_AUTH"]);
    }

    #[\Override]
    public function Session(): array {
        if (!$this->configured || !$this->IsLogged()) {

            return [];
        } else {

            return $_SESSION["_AUTH"];
        }
    }
}
