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

use CwfPhp\CwfPhp\Interfaces\Data\ConfigFileInterface;
use CwfPhp\CwfPhp\Data\Json;
use CwfPhp\CwfPhp\Data\Ini;

final class Config {

    private const DIR = \APP_CFG . \DS;

    public static function file(string $file): ConfigFileInterface {
        $fileParts = \explode(".", $file);
        $fileType = \end($fileParts);

        try {
            
            return match ($fileType) {
                "ini" => new Ini(self::DIR . $file),
                "json" => new Json(self::DIR . $file)
            };
        } catch (\UnhandledMatchError) {
            
            throw new \Error("CONFIG: unknown file type");
        }
    }
}
