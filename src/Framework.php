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

    private array $required_dirs = [
        "Config", "Controllers", "Data", "Models", "Public", "Views"
    ];
    private array $writeable_dirs = [
        "Config", "Data"
    ];
    private static bool $is_init = false;

    #[\Override]
    public function __construct(private string $application_path) {
        if (self::$is_init) {

            throw new \Error("CORE: cannot setup application more than once");
        }

        $this->Setup_Constants();
        $this->Setup_Handlers();
        $this->Check_Directories();
        $this->Setup_Classes();
        $this->Setup_Session();

        self::$is_init = true;
    }

    #[\Override]
    public static function Setup(string $application_path): void {

        new Framework($application_path);
    }

    private function Check_Directories(): void {
        foreach ($this->required_dirs as $dir) {
            if (!\is_dir(\APPDIR . \DS . $dir)) {

                throw new \Error("Core: missing required directory `{$dir}`");
            }
        }

        foreach ($this->writeable_dirs as $dir) {
            if (!\is_writable(\APPDIR . \DS . $dir)) {

                throw new \Error("CORE: directory '{$dir}' is not writable");
            }
        }
    }

    private function Setup_Constants(): void {
        \define("DS", \DIRECTORY_SEPARATOR);
        \define("CWFDIR", __DIR__);
        // application constants
        \define("APPDIR", $this->application_path);
        \define("CFGDIR", \APPDIR . \DS . "Config");
        \define("DATADIR", \APPDIR . \DS . "Data");
    }

    private function Setup_Classes(): void {
        Url::Setup();
    }

    private function Setup_Handlers(): void {
        $prefix = self::class;

        \set_error_handler("{$prefix}::Error_Handler");
        \set_exception_handler("{$prefix}::Exception_Handler");
    }

    private function Setup_Session(): void {
        /** @todo enhance session security */
        \session_start();
    }

    #[\Override]
    public static function Error_Handler(int $no,
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

    #[\Override]
    public static function Exception_Handler(\Throwable $ex): void {
        echo self::Static_Template("error", [
            "type" => $ex::class,
            "no" => $ex->getCode(),
            "file" => $ex->getFile(),
            "line" => $ex->getLine(),
            "message" => $ex->getMessage()
        ]);
    }

    private static function Static_Template(string $template,
            array $vars = []): string {

        $template = CWFDIR . DS . "static" . DS . "{$template}.html";

        if (!file_exists($template)) {

            throw new \Error("CORE: template '{$template}' does not exist");
        }

        $cnt = \file_get_contents($template);

        foreach ($vars as $var => $val) {
            $cnt = \str_replace("{\$$var}", $val, $cnt);
        }

        return $cnt;
    }
}
