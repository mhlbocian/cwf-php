<?php

/*
 * CWF-PHP Framework
 * 
 * File: Url.php
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp;

use CwfPhp\CwfPhp\Config;
use CwfPhp\CwfPhp\Interfaces\UrlInterface;

/**
 * Class for creating URLs for sites and resources
 */
final class Url implements UrlInterface {

    private static bool $configured = false;
    private static bool $rewrite;
    private static int $port;
    private static string $host;
    private static string $index;
    private static string $path;
    private static string $protocol;

    #[\Override]
    public static function redirect(string $path = ""): void {
        \header("Location: " . self::site($path));
        exit();
    }

    #[\Override]
    public static function resource(string $path): string {

        return self::make($path[0] != "/") . $path;
    }

    #[\Override]
    public static function site(string $path = ""): string {
        $url = self::make();

        if ($path == "") {

            return $url;
        }
        // check including `index` in the url
        if (!self::$rewrite) {
            $url .= self::$index;
        } else {
            $url = \substr($url, 0, -1);
        }
        // check if path is absolute or relative
        if ($path[0] == "/") {
            $url .= $path;
        } else {
            $ctrl = Router::getArgs(true)[0] ?? null;
            $url .= ($ctrl == null) ? "/{$path}" : "/{$ctrl}/{$path}";
        }

        return $url;
    }

    private static function make(bool $withPath = true): string {
        self::setup();

        $url = self::$protocol . "://";
        $url .= self::$host;

        if (!((self::$protocol == "http" && self::$port == 80) ||
                (self::$protocol == "https" && self::$port == 443))) {
            $url .= ":" . self::$port;
        }

        return $url . (($withPath) ? self::$path : "");
    }

    private static function setup(): void {
        if (self::$configured) {

            return;
        }

        if (!Config::file("url.json")->exists()) {

            throw new \Error("URL: no configuration file 'url.json'");
        }

        $config = Config::file("url.json")->fetch();

        if (!key_exists("host", $config)) {

            throw new \Error(
                            "URL: you must specify at least 'host' key in "
                            . "the 'url.json' configuration file");
        }

        self::$protocol = $config["protocol"] ?? "https";
        self::$host = $config["host"];
        self::$port = $config["port"] ?? "443";
        self::$path = $config["path"] ?? "/";
        self::$index = $config["index"] ?? "index.php";
        self::$rewrite = $config["rewrite"] ?? false;
        self::$configured = true;
    }
}
