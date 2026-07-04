<?php

/*
 * CWF-PHP Framework
 * 
 * File: Data.php
 * Description: Data class
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp;

use CwfPhp\CwfPhp\Data\Json;
use CwfPhp\CwfPhp\Data\Ini;

final class Data {

    private const DIR = \APP_DATA . \DS;

    public static function ini(string $file): Ini {

        return new Ini(self::DIR . "{$file}.ini");
    }

    public static function json(string $file): Json {

        return new Json(self::DIR . "{$file}.json");
    }
}
