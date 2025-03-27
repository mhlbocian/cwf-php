<?php

/*
 * CWF-PHP Framework
 * 
 * File: Url.php
 * Description: Framework\Url class
 * Author: Michał Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework;

use Framework\Config;

class Url {

    // default values
    private static string $protocol = "http";
    private static string $host = "localhost";
    private static int $port = 80;
    private static string $path = "/";
    private static string $index = "index.php";
    private static bool $omit_index = false;

    /**
     * Load values from config.json ("url" section), and replace in-class
     * defaults
     * 
     * @return void
     */
    public static function Configure(): void {
        $cfg = Config::Get("url") ?? null;
        if (isset($cfg["protocol"])) {
            self::$protocol = $cfg["protocol"];
        }
        if (isset($cfg["host"])) {
            self::$host = $cfg["host"];
        }
        if (isset($cfg["port"])) {
            self::$port = $cfg["port"];
        }
        if (isset($cfg["path"])) {
            self::$path = $cfg["path"];
        }
        if (isset($cfg["index"])) {
            self::$index = $cfg["index"];
        }
        if (isset($cfg["omit_index"])) {
            self::$omit_index = $cfg["omit_index"];
        }
    }

    /**
     * Method for creating URLs for local resources in docroot, like css,
     * images etc.
     * 
     * @param string $path Path to resource in docroot
     * @return string Full URL
     */
    public static function Local(string $path = ""): string {
        $url = self::$protocol . "://";
        $url .= self::$host;
        // omit port number if is protocol's default
        if (!((self::$protocol == "http" && self::$port == 80) ||
                (self::$protocol == "https" && self::$port == 443))) {
            $url .= ":" . self::$port;
        }
        $url .= self::$path . $path;

        return $url;
    }

    /**
     * Like "Local" method, but for creating urls for actions (check if
     * "index.php" ('index' in url config) must be included in address)
     * 
     * @param string $site
     * @return string Full URL to Controller/Action/[params]...
     */
    public static function Site(string $site): string {
        $url = self::Local();
        if (!self::$omit_index) {
            $url .= self::$index . "/";
        }
        $url .= $site;

        return $url;
    }
}
