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

    private static array $config_data = [];

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
    private static function Load(string $cfg) {
        $path = CFGDIR . DS . "{$cfg}.json";

        if (!self::Exists($cfg)) {
            throw new Exception("CONFIG: file '{$path}' does not exist");
        }

        $cnt = file_get_contents($path);
        self::$config_data[$cfg] = json_decode($cnt, true);
    }

    /**
     * Helper function for Fetch/Get. Check, if config file is already loaded.
     * If not, load it.
     * 
     * @param string|null $cfg
     * @return void
     */
    private static function Check_Load(string $cfg): void {
        if (!isset(self::$config_data[$cfg])) {
            self::Load($cfg);
        }
    }

    /**
     * Fetch all data from config file
     * 
     * @param string|null $cfg
     * @return array
     */
    public static function Fetch(string $cfg): array {
        self::Check_Load($cfg);

        return self::$config_data[$cfg];
    }

    /**
     * Get a value from configuration file
     * 
     * @param string $key Key from configuration
     * @param string|null $cfg Configuration file
     * @return mixed
     */
    public static function Get(string $cfg, string $key): mixed {
        self::Check_Load($cfg);

        return self::$config_data[$cfg][$key];
    }

    /**
     * Set a new data for local config file
     * 
     * @param string $key
     * @param mixed $value
     * @param string $cfg Config file name
     * @return void
     */
    public static function Set(string $cfg, string $key, mixed $value): void {
        /*
         * If the file exists and is not loaded - load it, to avoid wipeout
         * all other data
         */
        if (self::Exists($cfg) && !isset(self::$config_data[$cfg])) {
            self::Load();
        }

        self::$config_data[$cfg][$key] = $value;
    }

    /**
     * Update local config file with current data
     * 
     * @param string $cfg
     * @return void
     */
    public static function Update(string $cfg): void {
        if (!isset(self::$config_data[$cfg])) {
            return; // nothing to do
        }

        $path = CFGDIR . DS . "{$cfg}.json";

        if (!($fh = fopen($path, "w"))) {
            throw new Exception("CONFIG: cannot open file '{$path}'");
        }

        fwrite($fh, json_encode(self::$config_data[$cfg]));
        fclose($fh);
    }
}
