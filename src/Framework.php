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

use CwfPhp\CwfPhp\Interfaces\Framework as IFramework;

final class Framework implements IFramework {

    private static array $app_env = [];
    private static array $app_reqdirs = [
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
    private static ?Framework $instance = null;

    #[\Override]
    public function __construct(private readonly string $app_path) {
        if (!is_null(self::$instance)) {

            throw new \Error("CORE: cannot setup application more than once");
        }

        self::$instance = $this;
        $this->Setup_Constants();
        $this->Setup_Handlers();
        $this->Setup_Directories();
        $this->Setup_Classes();
        $this->Setup_Session();
    }

    #[\Override]
    public static function App_Init(string $app_path): void {

        new Framework($app_path);
    }

    #[\Override]
    public static function Get_Env(?string $key = null): array {
        if (is_null($key)) {

            return self::$app_env;
        }

        if (!key_exists($key, self::$app_env)) {

            throw new \Error("CORE: environment key '{$key}' doesn't exist");
        }

        return self::$app_env[$key];
    }

    #[\Override]
    public static function Set_Dir(string $type, string $name): void {
        if (!key_exists($type, self::$app_reqdirs)) {

            throw new \Error("CORE: {$type} is not a valid type of directory");
        }

        self::$app_reqdirs[$type]["name"] = $name;
    }

    #[\Override]
    public static function Set_Env(string $key, mixed $value): void {
        if (key_exists($key, self::$app_env)) {

            throw new \Error("CORE: environment key '{$key}' already exists");
        }

        self::$app_env[$key] = $value;
    }

    private function Setup_Constants(): void {
        \define("DS", \DIRECTORY_SEPARATOR);

        $contants = [
            "CWF_ROOT" => __DIR__,
            "APP_ROOT" => $this->app_path,
        ];

        foreach (self::$app_reqdirs as $dir) {
            if (\key_exists("const", $dir)) {
                $contants[$dir['const']] = $this->app_path . \DS . $dir["name"];
            }
        }

        foreach ($contants as $name => $value) {
            \define($name, $value);
            self::Set_Env($name, $value);
        }
    }

    private function Setup_Directories(): void {
        $missing_dirs = [];

        foreach (self::$app_reqdirs as $type => $dir) {
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

    private function Setup_Classes(): void {
        Url::Setup();
    }

    private function Setup_Handlers(): void {
        $namespace = "CwfPhp\\CwfPhp\\Handlers";

        \set_error_handler("{$namespace}::Error_Handler");
        \set_exception_handler("{$namespace}::Exception_Handler");
    }

    private function Setup_Session(): void {
        /** @todo enhance session security */
        \session_start();
    }
}
