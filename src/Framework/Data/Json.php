<?php

/*
 * CWF-PHP Framework
 * 
 * File: Framework\Data\Json.php
 * Description: Data manipulation - JSON files
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Data;

final class Json implements \Framework\Interfaces\Data_Json {

    private const string JSONDIR = \DATADIR;

    /**
     * 
     * @var array JSON data
     */
    private static array $data = [];

    /**
     * Check if configuration file exists
     * 
     * @param string $file JSON file name
     * @return bool
     */
    #[\Override]
    public static function Exists(string $file): bool {

        return \file_exists(self::JSONDIR . \DS . "{$file}.json");
    }

    /**
     * Fetch all data from configuration file
     * 
     * @param string $file JSON file name
     * @return array Decoded JSON array of whole configuration file
     */
    #[\Override]
    public static function Fetch(string $file): array {
        self::Check_Load($file);

        return self::$data[$file];
    }

    /**
     * Get a value from JSON file
     * 
     * @param string $file JSON file name
     * @param string $key Key from configuration file
     * @return mixed Contents
     */
    #[\Override]
    public static function Get(string $file, string $key): mixed {
        self::Check_Load($file);

        if (!\key_exists($key, self::$data[$file])) {
            throw new \Exception("DATA-JSON: Key '{$key}' not found in '{$file}'");
        }

        return self::$data[$file][$key];
    }

    /**
     * Set a new data for a JSON file
     * 
     * @param string $cfg JSON file name
     * @param string $key Key to set
     * @param mixed $value
     * @return void
     */
    #[\Override]
    public static function Set(string $file, string $key, mixed $value): void {
        /*
         * If the file exists and is not loaded - load it, to avoid wipeout
         * all other data
         */
        if (self::Exists($file) && !isset(self::$data[$file])) {
            self::Load($file);
        }

        self::$data[$file][$key] = $value;
    }

    /**
     * Unset data in a JSON file
     * 
     * @param string $file JSON file name
     * @param string $key Key to unset
     * @return void
     */
    #[\Override]
    public static function Unset(string $file, string $key): void {
        /*
         * If the file exists and is not loaded - load it, to avoid wipeout
         * all other data
         */
        if (self::Exists($file) && !isset(self::$data[$file])) {
            self::Load($file);
        }

        unset(self::$data[$file][$key]);
    }

    /**
     * Update JSON file with current data
     * 
     * @param string $file JSON file name
     * @return void
     */
    #[\Override]
    public static function Update(string $file): void {
        if (!isset(self::$data[$file])) {
            return; // nothing to do
        }

        $path = self::JSONDIR . \DS . "{$file}.json";

        if (!($fh = \fopen($path, "w"))) {
            throw new \Exception("DATA-DIR: cannot open file '{$file}'");
        }

        \fwrite($fh, \json_encode(self::$data[$file]));
        \fclose($fh);
    }

    /**
     * Load JSON file contents
     * 
     * @param string $file Configuration file name
     * @throws Exception
     */
    private static function Load(string $file): void {
        $path = self::JSONDIR . \DS . "{$file}.json";

        if (!self::Exists($file)) {
            throw new \Exception("DATA-JSON: file '{$path}' does not exist");
        }

        $cnt = \file_get_contents($path);
        self::$data[$file] = \json_decode($cnt, true);
    }

    /**
     * Helper function for Fetch/Get. Check, if JSON file is already loaded.
     * If not, load it
     * 
     * @param string $file Configuration file name
     * @return void
     */
    private static function Check_Load(string $file): void {
        if (!isset(self::$data[$file])) {
            self::Load($file);
        }
    }
}
