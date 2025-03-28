<?php

/*
 * CWF-PHP Framework
 * 
 * File: Config.php
 * Description: Framework\Config class
 * Author: Michał Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework;

use Exception;

class Config {

    private static array $main_config = []; // for main config.json
    private static array $custom_config = []; // for custom configs

    /**
     * Check if custom config file exists
     * 
     * @param string $cfg
     * @return bool
     */
    public static function Exists(string $cfg): bool {
        return file_exists(CFGDIR . DS . "{$cfg}.json");
    }

    /**
     * Loads configuration file contents.
     * 
     * If main config file (config.json) load it to $main_config, else for
     * custom configs load it to $custom_config
     * 
     * @param string|null $cfg Configuration file to load.
     * @throws Exception
     */
    private static function Load(?string $cfg = null) {
        if ($cfg == null) { // if $cfg null, assume it is main config.json
            $path = ROOTDIR . DS . "config.json";
        } else {
            $path = CFGDIR . DS . "{$cfg}.json";
        }

        if (!file_exists($path)) {
            throw new Exception("File {$path} does not exist");
        }

        $cnt = file_get_contents($path);

        if ($cfg == null) {
            self::$main_config = []; // clear array if previously loaded
            self::$main_config = json_decode($cnt, true);
        } else {
            self::$custom_config[$cfg] = []; // clear array if previously loaded
            self::$custom_config[$cfg] = json_decode($cnt, true);
        }
    }

    /**
     * Helper function for Fetch/Get. Checks if config file is already loaded.
     * If not, loads it.
     * 
     * @param string|null $cfg
     * @return void
     */
    private static function Check_Load(?string $cfg): void {
        if ($cfg == null && empty(self::$main_config)) {
            self::Load();
        } else if ($cfg != null && !isset(self::$custom_config[$cfg])) {
            self::Load($cfg);
        }
    }

    /**
     * Fetch all data from config file
     * 
     * @param string|null $cfg
     * @return array
     */
    public static function Fetch(?string $cfg = null): array {
        self::Check_Load($cfg);

        if ($cfg == null) { // main config.json
            return self::$main_config;
        } else {
            return self::$custom_config[$cfg];
        }
    }

    /**
     * Gets a value from configuration file
     * 
     * @param string $key Key from configuration
     * @param string|null $cfg Configuration file
     * @return mixed
     */
    public static function Get(string $key, ?string $cfg = null): mixed {
        self::Check_Load($cfg);
        
        if ($cfg == null) { // main config.json
            return self::$main_config[$key];
        } else {
            return self::$custom_config[$cfg][$key];
        }
    }

    /**
     * Update local config file with current data
     * 
     * @param string $cfg
     * @return void
     */
    public static function Update(string $cfg): void {
        if (!isset(self::$custom_config[$cfg])) {
            return; // nothing to do
        }

        $path = CFGDIR . DS . "{$cfg}.json";

        if (!($fh = fopen($path, "w"))) {
            throw new Exception("Cannot open file {$path}. Check permissions");
        }

        fwrite($fh, json_encode(self::$custom_config[$cfg]));
        fclose($fh);
    }

    /**
     * Sets new data for local config file
     * 
     * @param string $key
     * @param mixed $value
     * @param string $cfg Config file name
     * @return void
     */
    public static function Set(string $key, mixed $value, string $cfg): void {
        /*
         * If the file exists and is not loaded - load it, to avoid wipeout
         * all other data
         */
        if (self::Exists($cfg) && !isset(self::$custom_config[$cfg])) {
            self::Load();
        }

        self::$custom_config[$cfg][$key] = $value;
    }
}
