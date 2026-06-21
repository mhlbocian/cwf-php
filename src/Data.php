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

final class Data {

    public static function Exists(string $file): bool {

        return \file_exists(\APP_DATA . \DS . "{$file}.json");
    }

    public static function Json(string $file): Json {
        $path = \APP_DATA . \DS . "{$file}.json";

        return new Json($path);
    }
}
