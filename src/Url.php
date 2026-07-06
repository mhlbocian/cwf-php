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

    /**
     * 
     * @var bool If true, loadConfig is omited
     */
    private static bool $configLoaded = false;

    /**
     * 
     * @var bool if true, it omits index.php
     */
    private static bool $rewrite;

    /**
     * 
     * @var int HTTP server port (default: 443)
     */
    private static int $port;

    /**
     * 
     * @var string HTTP server hostname
     */
    private static string $host;

    /**
     * 
     * @var string Name of the index file (default: index.php)
     */
    private static string $index;

    /**
     * 
     * @var string URL application path (default: /)
     */
    private static string $path;

    /**
     * 
     * @var string either http or https (default: https)
     */
    private static string $protocol;

    /**
     * Create base URL with or without application path.
     * 
     * @param bool $withPath include application path
     * @return string http[s]://hostname[:port][/aaa/bbb/.../]
     */
    #[\Override]
    public static function base(bool $withPath = false): string {

        return self::makeBase() . (($withPath) ? self::$path : "");
    }

    /**
     * Redirect to the correspoding *site*. By default it redirects to the
     * main page.
     * 
     * @param string|null $path
     * @return void
     */
    #[\Override]
    public static function redirect(?string $path = null): void {
        \header("Location: " . self::site($path));
        exit();
    }

    /**
     * Make URL for accessing resources (like subfolders, images). When the path
     * is absolute (begins with /), return the URL without application path.
     * Otherwise, lookup in the application path.
     * 
     * @param string $path
     * @return string
     */
    #[\Override]
    public static function resource(string $path): string {

        return self::makeBase($path[0] != "/") . $path;
    }

    /**
     * If path is null (by default) create URL for main page. If the path is
     * absolute, it is obvious, but when the path is relative (not begin with /)
     * it includes the current either controller or pointer (custom route).
     * 
     * @param string|null $path
     * @return string
     */
    #[\Override]
    public static function site(?string $path = null): string {
        $url = self::base(true);

        if (\is_null($path)) {

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

    /**
     * Create base for further URL functions
     * 
     * @return string http[s]://{hostname}[:port]
     */
    private static function makeBase(): string {
        self::loadConfig();

        $url = self::$protocol . "://";
        $url .= self::$host;

        if (!((self::$protocol == "http" && self::$port == 80) ||
                (self::$protocol == "https" && self::$port == 443))) {
            $url .= ":" . self::$port;
        }

        return $url;
    }

    /**
     * If any URL function is invoked first time, read config and load values
     * 
     * @return void
     * @throws \Error
     */
    private static function loadConfig(): void {
        if (self::$configLoaded) {

            return;
        }

        if (!Config::file("url.json")->exists()) {

            throw new \Error("[url.json] file not exists");
        }

        $config = Config::file("url.json")->fetch();

        if (!key_exists("host", $config)) {

            throw new \Error("[url.json] no 'host' key");
        }

        self::$protocol = $config["protocol"] ?? "https";
        self::$host = $config["host"];
        self::$port = $config["port"] ?? "443";
        self::$path = $config["path"] ?? "/";
        self::$index = $config["index"] ?? "index.php";
        self::$rewrite = $config["rewrite"] ?? false;
        self::$configLoaded = true;
    }
}
