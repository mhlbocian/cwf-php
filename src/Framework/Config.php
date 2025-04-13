<?php

/*
 * CWF-PHP Framework
 * 
 * File: Config.php
 * Description: Config files management
 * Author: Michał Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework;

use Exception;

class Config {

    private static array $main_config = []; // for ROOT/config.json
    private static array $rest_config = []; // for CFGDIR/*.json

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
     * Load configuration file contents.
     * 
     * If main config file (config.json) load it to $main_config, else for
     * other config file load it to $rest_config
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

        if ($cfg != null && !self::Exists($cfg)) {
            throw new Exception("CONFIG: file '{$path}' does not exist");
        }

        $cnt = file_get_contents($path);

        if ($cfg == null) {
            self::$main_config = json_decode($cnt, true);
        } else {
            self::$rest_config[$cfg] = json_decode($cnt, true);
        }
    }

    /**
     * Helper function for Fetch/Get. Check, if config file is already loaded.
     * If not, load it.
     * 
     * @param string|null $cfg
     * @return void
     */
    private static function Check_Load(?string $cfg): void {
        if ($cfg == null && empty(self::$main_config)) {
            self::Load();
        } else if ($cfg != null && !isset(self::$rest_config[$cfg])) {
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
            return self::$rest_config[$cfg];
        }
    }

    /**
     * Get a value from configuration file
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
            return self::$rest_config[$cfg][$key];
        }
    }
    
    /**
     * Set a new data for local config file
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
        if (self::Exists($cfg) && !isset(self::$rest_config[$cfg])) {
            self::Load();
        }

        self::$rest_config[$cfg][$key] = $value;
    }

    /**
     * Update local config file with current data
     * 
     * @param string $cfg
     * @return void
     */
    public static function Update(string $cfg): void {
        if (!isset(self::$rest_config[$cfg])) {
            return; // nothing to do
        }

        $path = CFGDIR . DS . "{$cfg}.json";

        if (!($fh = fopen($path, "w"))) {
            throw new Exception("CONFIG: cannot open file '{$path}'");
        }

        fwrite($fh, json_encode(self::$rest_config[$cfg]));
        fclose($fh);
    }
}
