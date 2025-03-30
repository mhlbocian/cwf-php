<?php

/*
 * CWF-PHP Framework
 * 
 * File: Url.php
 * Description: Class for actions on URLs
 * Author: Michał Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework;

use Framework\Config;

class Url {

    private static bool $omit_index;
    private static int $port;
    private static string $host;
    private static string $index;
    private static string $path;
    private static string $protocol;

    /**
     * Load values from config.json ("url" section)
     * 
     * @return void
     */
    public static function Load_Config(): void {
        $cfg = Config::Get("url");
        self::$protocol = $cfg["protocol"];
        self::$host = $cfg["host"];
        self::$port = $cfg["port"];
        self::$path = $cfg["path"];
        self::$index = $cfg["index"];
        self::$omit_index = $cfg["omit_index"];
    }

    /**
     * Method for creating URLs for sites and local resources.
     * To access local resource, set $site to false
     * 
     * @param string $path Path to resource in docroot
     * @return string Full URL
     */
    public static function Site(string $path = "", bool $site = true): string {
        $url = self::$protocol . "://";
        $url .= self::$host;
        // omit port number if is protocol's default
        if (!((self::$protocol == "http" && self::$port == 80) ||
                (self::$protocol == "https" && self::$port == 443))) {
            $url .= ":" . self::$port;
        }

        $url .= self::$path;

        if ($site && !self::$omit_index) {
            $url .= self::$index . "/";
        }

        $url .= $path;

        return $url;
    }

    /**
     * Redirects to specified page or resource (when $site is false)
     * 
     * @param string $path
     * @param bool $site
     * @return void
     */
    public static function Redirect(string $path = "", bool $site = true): void {
        header("Location: " . self::Site($path, $site));
        exit();
    }
}
