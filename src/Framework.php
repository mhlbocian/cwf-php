<?php

/*
 * CWF-PHP Framework
 * 
 * File: Framework.php
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp;

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
        "controllers" => [
            "name" => "Controllers",
            "writeable" => false,
        ],
        "data" => [
            "name" => "Data",
            "writeable" => true,
            "const" => "APP_DATA"
        ],
        "models" => [
            "name" => "Models",
            "writeable" => false
        ],
        "public" => [
            "name" => "Public",
            "writeable" => false,
        ],
        "views" => [
            "name" => "Views",
            "writeable" => false,
            "const" => "APP_VIEWS"
        ]
    ];

    /**
     * 
     * @var FrameworkInterface|null The instance of framework (signleton)
     */
    private static ?FrameworkInterface $instance = null;

    /**
     * Check if the framework is already initialised. If not, setup environment
     * 
     * @param string $appPath Application root directory
     * @throws \Error
     */
    #[\Override]
    public function __construct(private readonly string $appPath) {
        if (!is_null(self::$instance)) {

            throw new \Error("CORE: cannot setup application more than once");
        }

        self::$instance = $this;
        $this->setupConstants();
        $this->setupHandlers();
        $this->setupDirectories();
        $this->setupSession();
    }

    /**
     * Static function to initialise application
     * 
     * @param string $appPath Application root directory
     * @return void
     */
    #[\Override]
    public static function application(string $appPath): void {

        new Framework($appPath);
    }

    /**
     * Get single or all environmental value(s).
     * 
     * @param string|null $key If null, get all environmental values
     * @return mixed
     * @throws \Error
     */
    #[\Override]
    public static function getEnv(?string $key = null): mixed {
        if (\is_null($key)) {

            return self::$appEnv;
        }

        if (!\key_exists($key, self::$appEnv)) {

            throw new \Error("CORE: environment key '{$key}' doesn't exist");
        }

        return self::$appEnv[$key];
    }

    /**
     * Set custom name for required directory, instead of a default one
     * 
     * @param string $type Type of required directory (ie. config, data)
     * @param string $name Name for custom directory name
     * @return void
     * @throws \Error
     */
    #[\Override]
    public static function setDir(string $type, string $name): void {
        if (!\key_exists($type, self::$appReqDirs)) {

            throw new \Error("CORE: {$type} is not a valid type of directory");
        }

        self::$appReqDirs[$type]["name"] = $name;
    }

    /**
     * Set an environmental value. Throws error, if the key already exists
     * 
     * @param string $key Key name
     * @param mixed $value Key value
     * @return void
     * @throws \Error
     */
    #[\Override]
    public static function setEnv(string $key, mixed $value): void {
        if (\key_exists($key, self::$appEnv)) {

            throw new \Error("CORE: environment key '{$key}' already exists");
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
            "APP_ROOT" => $this->appPath,
        ];

        foreach (self::$appReqDirs as $dir) {
            if (\key_exists("const", $dir)) {
                $contants[$dir['const']] = $this->appPath . \DS . $dir["name"];
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
     * @throws \Error
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
                $errMsg = "CORE: the '{$type}' directory is not writeable";

                throw new \Error($errMsg);
            }
        }

        if (empty($missing_dirs)) {

            return;
        }

        $errMsg = "CORE: following directories '";
        $errMsg .= \implode(", ", $missing_dirs);
        $errMsg .= "' don't exist in application root directory.";

        throw new \Error($errMsg);
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
