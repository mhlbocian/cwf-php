<?php

/*
 * CWF-PHP Framework
 * 
 * File: Config.php
 * Description: Config files interface for JSON
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp;

use CwfPhp\CwfPhp\Data\Json;
use CwfPhp\CwfPhp\Data\Ini;

final class Config {

    private const DIR = \APP_CFG . \DS;

    public static function Ini(string $file): Ini {

        return new Ini(self::DIR . "{$file}.ini");
    }

    public static function Json(string $file): Json {
        
        return new Json(self::DIR . "{$file}.json");
    }
}
