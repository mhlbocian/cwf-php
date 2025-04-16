<?php

/*
 * CWF-PHP Framework
 * 
 * File: Core.php
 * Description: Core class of CWF-PHP framework.
 * Author: Michał Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework;

use Error;

class Core {

    /**
     * 
     * @var array Array of directories with required write permission
     */
    private static array $dirs_array = [
        CFGDIR,
        DATADIR,
    ];

    /**
     * 
     * @var array Array of required PHP modules
     */
    private static array $php_req_mods = [
        "filter",
        "json",
        "PDO",
        "session",
    ];

    /**
     * 
     * @var string Minimal required PHP version
     */
    private static string $php_min_ver = "8.3";

    /**
     * Used in bootstrap, to check all required stuff in application
     * environment, like directory permissions, PHP version and modules.
     * 
     * @return void
     */
    public static function Init(): void {
        self::Check_PHP_Env();
        self::Check_Dir_Perms();

        // initialise core classes
        Auth::Init();
        Url::Init();

        // initialize application info constants
        define("APPNAME", Config::Get("application", "application")["name"]);
        define("APPDES", Config::Get("application", "application")["description"]);
        define("APPVER", Config::Get("application", "application")["version"]);
    }

    /**
     * Check directories write permission
     * 
     * @param array $dirs
     * @return void
     * @throws Error
     */
    private static function Check_Dir_Perms(): void {
        foreach (self::$dirs_array as $dir) {
            if (!is_writable($dir)) {
                throw new Error("CORE: directory '{$dir}' is not writable");
            }
        }
    }

    /**
     * Check required PHP version and modules
     * 
     * @return void
     * @throws Error
     */
    private static function Check_PHP_Env(): void {
        if (!version_compare(PHP_VERSION, self::$php_min_ver, ">=")) {
            throw new Error("CORE: PHP version '" . PHP_VERSION . "' is older than required version "
                            . "'" . self::$php_min_ver . "'");
        }

        foreach (self::$php_req_mods as $module) {
            if (!extension_loaded($module)) {
                throw new Error("CORE: required PHP extension '{$module}' is not loaded");
            }
        }
    }
}
