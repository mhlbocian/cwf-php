<?php

/*
 * CWF-PHP Framework
 * 
 * File: Framework\Core.php
 * Description: Core class of CWF-PHP framework.
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework;

final class Core implements Interfaces\Core {

    private array $config;
    private array $dirs_array = [
        CFGDIR,
        DATADIR,
    ];
    private array $php_req_mods = [
        "filter",
        "json",
        "PDO",
        "session",
    ];
    private static ?Core $instance = null;
    private string $php_min_ver = "8.4";

    #[\Override]
    public static function Setup(): void {
        if (self::$instance == null) {
            self::$instance = new Core();
        } else {

            return;
        }
    }

    public function __construct() {
        $this->Check_Dir_Perms();
        $this->Check_PHP_Env();
        $this->Config_Core_Classes();

        $this->config = Config::File("application")->Fetch();
        $application = $this->config["application"];

        \define("APPNAME", $application["name"]);
        \define("APPDES", $application["description"]);
        \define("APPVER", $application["version"]);
    }

    private function Check_Dir_Perms(): void {
        foreach ($this->dirs_array as $dir) {
            if (!\is_writable($dir)) {
                throw new \Error("CORE: directory '{$dir}' is not writable");
            }
        }
    }

    private function Check_PHP_Env(): void {
        if (!\version_compare(\PHP_VERSION, $this->php_min_ver, ">=")) {
            throw new \Error("CORE: PHP version '" . PHP_VERSION . "' is older "
                            . "than required version '" . self::$php_min_ver . "'");
        }

        foreach ($this->php_req_mods as $module) {
            if (!\extension_loaded($module)) {
                throw new \Error("CORE: required PHP extension '{$module}' "
                                . "is not loaded");
            }
        }
    }

    private function Config_Core_Classes(): void {
        Auth::Instance();
        Url::Setup();
    }
}
