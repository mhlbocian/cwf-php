<?php

/*
 * CWF-PHP Framework
 * 
 * File: Config.php
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp;

use CwfPhp\CwfPhp\Interfaces\Data\ConfigFileInterface;
use CwfPhp\CwfPhp\Data\Json;
use CwfPhp\CwfPhp\Data\Ini;

/**
 * Class for manipulating config files (ini or json)
 */
final class Config {

    /** Config directory */
    private const DIR = \APP_CFG . \DS;

    /**
     * Determine the file type and return the object of right file type class.
     * 
     * @param string $file
     * @return ConfigFileInterface
     * @throws \Error
     */
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
