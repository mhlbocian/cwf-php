<?php

/*
 * CWF-PHP Framework
 * 
 * File: Handlers.php
 * Description: Error/Exception handlers class.
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp;

use CwfPhp\CwfPhp\Interfaces\HandlersInterface;

class Handlers implements HandlersInterface {

    #[\Override]
    public static function errorHandler(
            int $no,
            string $str,
            string $file,
            int $line): void {

        echo self::staticTemplate("error", [
            "type" => "Error",
            "no" => $no,
            "file" => $file,
            "line" => $line,
            "message" => $str
        ]);
    }

    #[\Override]
    public static function exceptionHandler(\Throwable $ex): void {
        echo self::staticTemplate("error", [
            "type" => $ex::class,
            "no" => $ex->getCode(),
            "file" => $ex->getFile(),
            "line" => $ex->getLine(),
            "message" => $ex->getMessage()
        ]);
    }

    private static function staticTemplate(string $template,
            array $vars = []): string {

        $template = \CWF_ROOT . \DS . "static" . \DS . "{$template}.html";

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
