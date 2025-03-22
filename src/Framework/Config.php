<?php

namespace Framework;

class Config {

    private const string CFGDIR = APPDIR . DS . "Application" . DS . "Config";

    private static array $app_config = []; // for main config.json
    private static array $custom_config = []; // for custom configs

    public static function Load(?string $cfg = null) {
        if ($cfg == null) { // if $cfg null, load main config.json file
            $path = APPDIR . DS . "config.json";
        } else {
            $path = self::CFGDIR . DS . "{$cfg}.json";
        }

        if (!file_exists($path)) {
            throw new \Exception("File {$path} does not exist!");
        }

        $cnt = file_get_contents($path);
        if ($cfg == null) {
            self::$app_config = []; // clear array if previously loaded
            self::$app_config = json_decode($cnt, true);
        } else {
            self::$custom_config[$cfg] = []; // clear array if previously loaded
            self::$custom_config[$cfg] = json_decode($cnt, true);
        }
    }

    public static function Get(string $key, ?string $cfg = null): mixed {
        if ($cfg == null) { // main config.json
            if (empty(self::$app_config)) { // if config.json is not yet loaded
                self::Load();
            }

            return self::$app_config[$key];
        } else {
            if (!key_exists($cfg, self::$custom_config)) { // try load config
                self::Load($cfg);
            }

            return self::$custom_config[$cfg][$key];
        }
    }
}
