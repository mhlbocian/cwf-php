<?php

/*
 * CWF-PHP Framework
 * 
 * File: Framework.php
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp;

use CwfPhp\CwfPhp\Exceptions\FrameworkException;
use CwfPhp\CwfPhp\Interfaces\FrameworkInterface;

/**
 * Core class of the CWF-PHP framework
 */
final class Framework implements FrameworkInterface {

    /**
     * 
     * @var array Enviromental values that can be reached from app code
     */
    private static array $appEnv = [];

    /**
     * "type_of_directory" => [
     *     "name" => "name_of_directory",
     *     "writeable" => "check_write_permissions",
     *     "const" => "name_of_constant_if_required"
     * ]
     * 
     * @var array Required directories for application
     */
    private static array $appReqDirs = [
        "config" => [
            "name" => "Config",
            "writeable" => true,
            "const" => "APP_CFG"
        ],
        "data" => [
            "name" => "Data",
            "writeable" => true,
            "const" => "APP_DATA"
        ],
        "public" => [
            "name" => "Public",
            "writeable" => false,
            "const" => "APP_PUBLIC"
        ],
        "sites" => [
            "name" => "Sites",
            "writeable" => false,
            "const" => "APP_SITES"
        ],
        "views" => [
            "name" => "Views",
            "writeable" => false,
            "const" => "APP_VIEWS"
        ]
    ];
    private static bool $initalised = false;

    /**
     * Check if the framework is already initialised. If not, setup environment
     * 
     * @param string $appDir Application root directory
     * @throws FrameworkException
     */
    #[\Override]
    public function __construct(private readonly string $appDir) {
        if (self::$initalised) {

            throw new FrameworkException(__CLASS__,
                            "framework is already initialised");
        }

        $this->setupConstants();
        $this->setupHandlers();
        $this->setupDirectories();
        $this->setupSession();

        self::$initalised = true;
    }

    /**
     * Static function to initialise application
     * 
     * @param string $appDir Application root directory
     * @return void
     */
    #[\Override]
    public static function application(string $appDir): void {

        new Framework($appDir);
    }
    
    /**
     * Method for optional modules, that needs framwework to work.
     * 
     * @return void
     * @throws FrameworkException
     */
    #[\Override]
    public static function checkEnv(): void {
        if (!self::$initalised) {

            throw new FrameworkException(__CLASS__,
                            "the application is not initialised");
        }
    }

    /**
     * Get single or all environmental value(s).
     * 
     * @param string|null $key If null, get all environmental values
     * @return mixed
     */
    #[\Override]
    public static function getEnv(?string $key = null): mixed {
        if (\is_null($key)) {

            return self::$appEnv;
        }

        if (!\key_exists($key, self::$appEnv)) {

            return null;
        }

        return self::$appEnv[$key];
    }

    /**
     * Set custom name for required directory, instead of a default one
     * 
     * @param string $type Type of required directory (ie. config, data)
     * @param string $name Name for custom directory name
     * @return void
     * @throws FrameworkException
     */
    #[\Override]
    public static function setDir(string $type, string $name): void {
        if (!\key_exists($type, self::$appReqDirs)) {

            throw new FrameworkException(__CLASS__,
                            "'{$type}' is not a valid directory type");
        }

        self::$appReqDirs[$type]["name"] = $name;
    }

    /**
     * Set an environmental value. Throws error, if the key already exists
     * 
     * @param string $key Key name
     * @param mixed $value Key value
     * @return void
     * @throws FrameworkException
     */
    #[\Override]
    public static function setEnv(string $key, mixed $value): void {
        if (\key_exists($key, self::$appEnv)) {

            throw new FrameworkException(__CLASS__,
                            "environment key '{$key}' already exists");
        }

        self::$appEnv[$key] = $value;
    }

    /**
     * Sets up all the required constants, like CWF_ROOT (framework root),
     * APP_ROOT (application root) and for other directories.
     * 
     * @return void
     */
    private function setupConstants(): void {
        \define("DS", \DIRECTORY_SEPARATOR);

        $contants = [
            "CWF_ROOT" => __DIR__,
            "APP_ROOT" => $this->appDir,
        ];

        foreach (self::$appReqDirs as $dir) {
            if (\key_exists("const", $dir)) {
                $contants[$dir['const']] = $this->appDir . \DS . $dir["name"];
            }
        }

        foreach ($contants as $name => $value) {
            \define($name, $value);
            self::setEnv($name, $value);
        }
    }

    /**
     * Check if the directories exist and check the required permissions to them
     * 
     * @return void
     * @throws FrameworkException
     */
    private function setupDirectories(): void {
        $missing_dirs = [];

        foreach (self::$appReqDirs as $type => $dir) {
            $path = \APP_ROOT . \DS . $dir["name"];
            if (!\is_dir($path)) {
                $missing_dirs[] = $dir["name"];

                continue;
            }

            if ($dir["writeable"] && !is_writeable($path)) {

                throw new FrameworkException(__CLASS__,
                                "the '{$type}' directory is not writeable");
            }
        }

        if (empty($missing_dirs)) {

            return;
        }

        $errMsg = "following directories '";
        $errMsg .= \implode(", ", $missing_dirs);
        $errMsg .= "' don't exist in the application root directory.";

        throw new FrameworkException(__CLASS__, $errMsg);
    }

    /**
     * Sets up error and exception handlers
     * 
     * @return void
     */
    private function setupHandlers(): void {
        $namespace = "CwfPhp\\CwfPhp\\Handlers";

        \set_error_handler("{$namespace}::errorHandler");
        \set_exception_handler("{$namespace}::exceptionHandler");
    }

    /**
     * Sets up a session
     * 
     * @return void
     */
    private function setupSession(): void {
        /** @todo enhance session security */
        \session_start();
    }
}
