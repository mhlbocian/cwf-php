<?php

/*
 * CWF-PHP Framework
 * 
 * File: Framework\Config.php
 * Description: Config files management
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework;

final class Config implements Interfaces\Config {

    /**
     * 
     * @var array Array for storing decoded JSON data
     */
    private static array $config_data = [];

    /**
     * Check if configuration file exists
     * 
     * @param string $cfg
     * @return bool
     */
    #[\Override]
    public static function Exists(string $cfg): bool {
        return \file_exists(\CFGDIR . \DS . "{$cfg}.json");
    }

    /**
     * Fetch all data from configuration file
     * 
     * @param string $cfg Configuration file name
     * @return array Decoded JSON array of whole configuration file
     */
    #[\Override]
    public static function Fetch(string $cfg): array {
        self::Check_Load($cfg);

        return self::$config_data[$cfg];
    }

    /**
     * Get a value from configuration file
     * 
     * @param string $cfg Configuration file name
     * @param string $key Key from configuration file
     * @return mixed Contents
     */
    #[\Override]
    public static function Get(string $cfg, string $key): mixed {
        self::Check_Load($cfg);

        if (!\key_exists($key, self::$config_data[$cfg])) {
            throw new \Exception("CONFIG: Key '{$key}' not found in '{$cfg}'");
        }

        return self::$config_data[$cfg][$key];
    }

    /**
     * Set a new data for local configuration file
     * 
     * @param string $cfg Configuration file name
     * @param string $key
     * @param mixed $value
     * @return void
     */
    #[\Override]
    public static function Set(string $cfg, string $key, mixed $value): void {
        /*
         * If the file exists and is not loaded - load it, to avoid wipeout
         * all other data
         */
        if (self::Exists($cfg) && !isset(self::$config_data[$cfg])) {
            self::Load($cfg);
        }

        self::$config_data[$cfg][$key] = $value;
    }

    /**
     * Update local configuration file with current data
     * 
     * @param string $cfg Configuration file name
     * @return void
     */
    #[\Override]
    public static function Update(string $cfg): void {
        if (!isset(self::$config_data[$cfg])) {
            return; // nothing to do
        }

        $path = \CFGDIR . \DS . "{$cfg}.json";

        if (!($fh = \fopen($path, "w"))) {
            throw new \Exception("CONFIG: cannot open file '{$path}'");
        }

        \fwrite($fh, \json_encode(self::$config_data[$cfg]));
        \fclose($fh);
    }

    /**
     * Load configuration file contents
     * 
     * @param string $cfg Configuration file name
     * @throws Exception
     */
    private static function Load(string $cfg): void {
        $path = \CFGDIR . \DS . "{$cfg}.json";

        if (!self::Exists($cfg)) {
            throw new \Exception("CONFIG: file '{$path}' does not exist");
        }

        $cnt = \file_get_contents($path);
        self::$config_data[$cfg] = \json_decode($cnt, true);
    }

    /**
     * Helper function for Fetch/Get. Check, if configuration file is already
     * loaded. If not, load it
     * 
     * @param string $cfg Configuration file name
     * @return void
     */
    private static function Check_Load(string $cfg): void {
        if (!isset(self::$config_data[$cfg])) {
            self::Load($cfg);
        }
    }
}
