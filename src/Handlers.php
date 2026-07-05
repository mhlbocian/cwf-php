<?php

/*
 * CWF-PHP Framework
 * 
 * File: Handlers.php
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp;

use CwfPhp\CwfPhp\Interfaces\HandlersInterface;

/**
 * Error/Exception handlers class
 */
class Handlers implements HandlersInterface {

    /**
     * CWF-PHP error handler
     * 
     * @param int $no
     * @param string $errMsg
     * @param string $file
     * @param int $line
     * @return void
     */
    #[\Override]
    public static function errorHandler(
            int $no,
            string $errMsg,
            string $file,
            int $line): void {

        \ob_start();
        \debug_print_backtrace();
        
        $debugBacktrace = \ob_get_clean();
        
        echo self::printMsg([
            "type" => "Error",
            "no" => $no,
            "file" => $file,
            "line" => $line,
            "message" => $errMsg,
            "trace" => $debugBacktrace
        ]);
    }

    /**
     * CWF-PHP exception handler
     * 
     * @param \Throwable $ex
     * @return void
     */
    #[\Override]
    public static function exceptionHandler(\Throwable $ex): void {
        echo self::printMsg([
            "type" => $ex::class,
            "no" => $ex->getCode(),
            "file" => $ex->getFile(),
            "line" => $ex->getLine(),
            "message" => $ex->getMessage(),
            "trace" => $ex->getTraceAsString()
        ]);
    }

    /**
     * Prepares the error/debug template and replaces in-file variables, with
     * right values. If APP_DEBUG env variable is defined, prints more detailed
     * information about execution error. Otherwise, more safe fixed message is
     * displayed.
     * 
     * @param array $vars Array of in-file variables to replace
     * @return string
     */
    private static function printMsg(array $vars): string {
        if (Framework::getEnv("APP_DEBUG")) {
            $template = \CWF_ROOT . \DS . "debug.html";
        } else {
            $template = \CWF_ROOT . \DS . "error.html";
        }

        $cnt = \file_get_contents($template);
        $cnt = \str_replace("{\$timestamp}", \date("Y.m.d H:m:s"), $cnt);

        foreach ($vars as $var => $val) {
            $cnt = \str_replace("{\$$var}", $val ?? "", $cnt);
        }

        return $cnt;
    }
}
