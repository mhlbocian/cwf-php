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

    public static function ini(string $fileName): Ini {

        return new Ini(self::DIR . "{$fileName}.ini");
    }

    public static function json(string $fileName): Json {

        return new Json(self::DIR . "{$fileName}.json");
    }

    public static function mkdir(string $dirName): void {
        $dirPath = self::DIR . $dirName;

        if (file_exists($dirPath)) {

            return;
        }

        if (!\mkdir($dirPath, recursive: true)) {

            throw new \Error("DATA: cannot create the directory '{$dirName}'");
        }
    }
}
