<?php

/*
 * CWF-PHP Framework
 * 
 * File: Framework\Url.php
 * Description: Class for actions on URLs
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework;

use Framework\Config;

class Url {

    /**
     * 
     * @var bool When true, generate URL without `index.php` for sites
     */
    private static bool $omit_index;
    
    /**
     * 
     * @var int Server port
     */
    private static int $port;
    
    /**
     * 
     * @var string Server host
     */
    private static string $host;
    
    /**
     * 
     * @var string Index file name (usually `index.php`)
     */
    private static string $index;
    
    /**
     * 
     * @var string HTTP `DOCROOT` path (usually `/`)
     */
    private static string $path;
    
    /**
     * 
     * @var string HTTP or HTTPS
     */
    private static string $protocol;

    /**
     * Load values from `CFGDIR/application.json` (`url` section)
     * 
     * @return void
     */
    public static function Init(): void {
        $url_cfg = Config::Fetch("application")["url"];
        self::$protocol = $url_cfg["protocol"];
        self::$host = $url_cfg["host"];
        self::$port = $url_cfg["port"];
        self::$path = $url_cfg["path"];
        self::$index = $url_cfg["index"];
        self::$omit_index = $url_cfg["omit_index"];
    }

    /**
     * Method for creating URLs for sites and local resources.
     * To access local resource, set $site to false
     * 
     * @param string $path Site or resource path
     * @param bool $site For accessing resources outside app, set false
     * @return string Full URL
     */
    public static function Site(string $path = "", bool $site = true): string {
        $url = self::$protocol . "://";
        $url .= self::$host;
        // omit port number if it's protocol's default
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
     * Redirect to specified page or resource (when $site is false)
     * 
     * @param string $path Site or resource path
     * @param bool $site For accessing resources outside app, set false
     * @return void
     */
    public static function Redirect(string $path = "", bool $site = true): void {
        \header("Location: " . self::Site($path, $site));
        exit();
    }
}
