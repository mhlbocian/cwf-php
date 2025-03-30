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

    private static array $dirs_array = [
        CFGDIR,
        DATADIR,
    ];
    private static array $php_req_mods = [
        "filter",
        "json",
        "PDO",
        "session",
    ];
    private static string $php_min_ver = "8.3";

    /**
     * Used in bootstrap, to check all required stuff in application
     * environment, like directory permissions, PHP version and modules.
     * 
     * @return void
     */
    public static function Check_Env(): void {
        self::Check_PHP_Env();
        self::Check_Dir_Perms();
    }

    /**
     * Checks directory write permission
     * 
     * @param array $dirs
     * @return void
     * @throws Error
     */
    private static function Check_Dir_Perms(): void {
        foreach (self::$dirs_array as $dir) {
            if (!is_writable($dir)) {
                throw new Error("Directory '{$dir}' is not writable.");
            }
        }
    }

    private static function Check_PHP_Env(): void {
        if (!version_compare(PHP_VERSION, self::$php_min_ver, ">=")) {
            throw new Error("PHP version '" . PHP_VERSION . "' is older "
                            . "than required version '" . self::$php_min_ver
                            . "'");
        }

        foreach (self::$php_req_mods as $module) {
            if (!extension_loaded($module)) {
                throw new Error("Required PHP extension '{$module}' is not "
                                . "loaded");
            }
        }
    }
}
