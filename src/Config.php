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

final class Config {

    public static function Exists(string $file): bool {

        return \file_exists(\APP_CFG . \DS . "{$file}.json");
    }

    public static function File(string $file): Json {
        $path = \APP_CFG . \DS . "{$file}.json";

        return new Json($path);
    }
}
