<?php

/*
 * CWF-PHP Framework
 * 
 * File: Config.php
 * Description: Config files interface for JSON
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Mhlbocian\CwfPhp;

use Mhlbocian\CwfPhp\Data\Json;

final class Config {

    private const string CFGDIR = \CFGDIR;

    public static function Exists(string $file): bool {

        return \file_exists(self::CFGDIR . \DS . "{$file}.json");
    }

    public static function File(string $file): Json {
        $path = self::CFGDIR . \DS . "{$file}.json";

        return new Json($path);
    }
}
