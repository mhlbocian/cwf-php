<?php

/*
 * CWF-PHP Framework
 * 
 * File: Url.php
 * Description: Class for actions on URLs
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp;

use CwfPhp\CwfPhp\Config;

final class Url implements Interfaces\Url {

    private static bool $omit_index;
    private static int $port;
    private static string $host;
    private static string $index;
    private static string $path;
    private static string $protocol;

    #[\Override]
    public static function Setup(): void {
        if (!Config::Json("url")->Exists()) {

            throw new \Error("URL: no configuration file 'url.json'");
        }

        $config = Config::Json("url")->Fetch();

        if (!key_exists("host", $config)) {

            throw new \Error("URL: you must specify at least 'host' key in "
                            . "the 'url.json' configuration file");
        }

        self::$protocol = $config["protocol"] ?? "https";
        self::$host = $config["host"];
        self::$port = $config["port"] ?? "443";
        self::$path = $config["path"] ?? "/";
        self::$index = $config["index"] ?? "index.php";
        self::$omit_index = $config["omit_index"] ?? false;
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
