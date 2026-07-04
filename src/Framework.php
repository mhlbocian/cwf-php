<?php

/*
 * CWF-PHP Framework
 * 
 * File: Framework.php
 * Description: Core class of CWF-PHP framework.
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp;

use CwfPhp\CwfPhp\Interfaces\FrameworkInterface;

final class Framework implements FrameworkInterface {

    private static array $appEnv = [];
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
    private static ?FrameworkInterface $instance = null;

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

    #[\Override]
    public static function application(string $appPath): void {

        new Framework($appPath);
    }

    #[\Override]
    public static function getEnv(?string $key = null): array {
        if (is_null($key)) {

            return self::$appEnv;
        }

        if (!key_exists($key, self::$appEnv)) {

            throw new \Error("CORE: environment key '{$key}' doesn't exist");
        }

        return self::$appEnv[$key];
    }

    #[\Override]
    public static function setDir(string $type, string $name): void {
        if (!key_exists($type, self::$appReqDirs)) {

            throw new \Error("CORE: {$type} is not a valid type of directory");
        }

        self::$appReqDirs[$type]["name"] = $name;
    }

    #[\Override]
    public static function setEnv(string $key, mixed $value): void {
        if (key_exists($key, self::$appEnv)) {

            throw new \Error("CORE: environment key '{$key}' already exists");
        }

        self::$appEnv[$key] = $value;
    }

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

    private function setupDirectories(): void {
        $missing_dirs = [];

        foreach (self::$appReqDirs as $type => $dir) {
            $path = \APP_ROOT . \DS . $dir["name"];
            if (!is_dir($path)) {
                $missing_dirs[] = $dir["name"];

                continue;
            }

            if ($dir["writeable"] && !is_writeable($path)) {
                $err_msg = "CORE: the '{$type}' directory is not writeable";

                throw new \Error($err_msg);
            }
        }

        if (empty($missing_dirs)) {

            return;
        }

        $err_msg = "CORE: following directories '";
        $err_msg .= implode(", ", $missing_dirs);
        $err_msg .= "' don't exist in application root directory.";

        throw new \Error($err_msg);
    }

    private function setupHandlers(): void {
        $namespace = "CwfPhp\\CwfPhp\\Handlers";

        \set_error_handler("{$namespace}::errorHandler");
        \set_exception_handler("{$namespace}::exceptionHandler");
    }

    private function setupSession(): void {
        /** @todo enhance session security */
        \session_start();
    }
}
