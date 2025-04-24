<?php

/*
 * CWF-PHP Framework
 * 
 * File: Core.php
 * Description: Core class of CWF-PHP framework.
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Mhlbocian\CwfPhp;

final class Core {

    private array $dirs_array = [
        "Config", "Data"
    ];
    private array $php_req_mods = [
        "filter", "json", "PDO", "session",
    ];
    private static ?Core $instance = null;
    private string $php_ver = "8.4";

    public function __construct() {
        $this->Setup_Consts();
        $this->Setup_Handlers();
        $this->Check_PHPEnv();
        $this->Check_DirPerms();
        $this->Setup_Classes();
        $this->Setup_Session();
    }

    private function Check_DirPerms(): void {
        foreach ($this->dirs_array as $dir) {
            if (!\is_writable(\APPDIR . \DS . $dir)) {

                throw new \Error("CORE: directory '{$dir}' is not writable");
            }
        }
    }

    private function Check_PHPEnv(): void {
        if (!\version_compare(\PHP_VERSION, $this->php_ver, ">=")) {

            throw new \Error("CORE: your PHP version is too old "
                            . "(required version {$this->php_ver}+)");
        }

        foreach ($this->php_req_mods as $module) {
            if (!\extension_loaded($module)) {

                throw new \Error("CORE: required PHP extension '{$module}' "
                                . "is not loaded");
            }
        }
    }

    public static function Setup(): void {
        if (!\defined("APPDIR")) {

            throw new \Error("CORE: define `APPDIR` constant before setup");
        }

        if (self::$instance == null) {
            self::$instance = new Core();
        } else {

            return;
        }
    }

    private function Setup_Consts(): void {
        \define("DS", \DIRECTORY_SEPARATOR);
        \define("CWFDIR", __DIR__);
        \define("CFGDIR", \APPDIR . \DS . "Config");
        \define("DATADIR", \APPDIR . \DS . "Data");
    }

    private function Setup_Classes(): void {
        Auth::Instance();
        Url::Setup();
    }

    private function Setup_Handlers(): void {
        $prefix = self::class;

        \set_error_handler("{$prefix}::Static_ErrorHandler");
        \set_exception_handler("{$prefix}::Static_ExceptionHandler");
    }

    private function Setup_Session(): void {
        /** @todo enhance session security */
        \session_start();
    }

    public static function Static_ErrorHandler(int $no,
            string $str,
            string $file,
            int $line): void {

        echo self::Static_Template("error", [
            "type" => "Error",
            "no" => $no,
            "file" => $file,
            "line" => $line,
            "message" => $str
        ]);
    }

    public static function Static_ExceptionHandler(\Throwable $ex): void {
        echo self::Static_Template("error", [
            "type" => $ex::class,
            "no" => $ex->getCode(),
            "file" => $ex->getFile(),
            "line" => $ex->getLine(),
            "message" => $ex->getMessage()
        ]);
    }

    private static function Static_GetFile(string $filename): string {
        if (!file_exists($path = CWFDIR . DS . "Static" . DS . "{$filename}")) {

            throw new \Error("CORE: static file '{$path}' does not exist");
        }

        return file_get_contents($path);
    }

    private static function Static_Template(string $template,
            array $vars = []): string {

        $cnt = self::Static_GetFile("{$template}.html");

        foreach ($vars as $var => $val) {
            $cnt = \str_replace("{\$$var}", $val, $cnt);
        }

        return $cnt;
    }
}
