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

    private const string DATADIR = \DATADIR;

    public static function Exists(string $file): bool {

        return \file_exists(self::DATADIR . \DS . "{$file}.json");
    }

    public static function Json(string $file): Json {
        $path = self::DATADIR . \DS . "{$file}.json";

        return new Json($path);
    }
}
