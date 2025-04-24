<?php

/*
 * CWF-PHP Framework
 * 
 * File: Url.php
 * Description: Class for actions on URLs
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Mhlbocian\CwfPhp;

use Mhlbocian\CwfPhp\Config;

final class Url implements Interfaces\Url {
    
    private static bool $omit_index;
    private static int $port;
    private static string $host;
    private static string $index;
    private static string $path;
    private static string $protocol;
    
    #[\Override]
    public static function Setup(): void {
        $url_cfg = Config::File("application")->Get("url");
        self::$protocol = $url_cfg["protocol"];
        self::$host = $url_cfg["host"];
        self::$port = $url_cfg["port"];
        self::$path = $url_cfg["path"];
        self::$index = $url_cfg["index"];
        self::$omit_index = $url_cfg["omit_index"];
    }
    
    #[\Override]
    public static function Resource(string $path): string {

        return self::Url($path[0] != "/") . $path;
    }
    
    #[\Override]
    public static function Site(string $path = ""): string {
        $url = self::Url();

        if ($path == "") {

            return $url;
        }
        // check including `index` in the url
        if (!self::$omit_index) {
            $url .= self::$index . "/";
        }
        // check if path is absolute or relative
        if ($path[0] == "/") {
            $path = \substr($path, 1);
            $url .= $path;
        } else {
            $url .= \explode("/", Router::Get_Route())[1] . "/{$path}";
        }

        return $url;
    }
    
    #[\Override]
    public static function Redirect(string $path = ""): void {
        \header("Location: " . self::Site($path));
        exit();
    }
    
    private static function Url(bool $with_path = true): string {
        $url = self::$protocol . "://";
        $url .= self::$host;
        // omit port number if it's protocol's default
        if (!((self::$protocol == "http" && self::$port == 80) ||
                (self::$protocol == "https" && self::$port == 443))) {
            $url .= ":" . self::$port;
        }

        return $url . (($with_path) ? self::$path : "");
    }
}
