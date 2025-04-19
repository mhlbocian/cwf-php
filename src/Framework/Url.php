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

final class Url implements Interfaces\Url {

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
    #[\Override]
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
     * Create URL for resource. If path string beigns with `/` return absolute
     * path (without `path` value in the `url` section in application.json file
     * 
     * @param string $path
     * @return string
     */
    public static function Resource(string $path): string {

        return self::Url($path[0] != "/") . $path;
    }

    /**
     * Create URL for site
     * If the path string begins with `/`, return absoulute address. Otherwise,
     * relative address is returned (with current controller name)
     * 
     * @param string $path Site or resource path
     * @return string Full URL
     */
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

    /**
     * Redirect to specified site
     * 
     * @param string $path Site
     * @return void
     */
    #[\Override]
    public static function Redirect(string $path = ""): void {
        \header("Location: " . self::Site($path));
        exit();
    }

    /**
     * Return URL string in the form: [protocol]://[hostname]:[port]/[path]
     * For HTTP:80, HTTPS:443 omit port number
     * 
     * @param bool $with_path Add url:path to string
     * @return string
     */
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
