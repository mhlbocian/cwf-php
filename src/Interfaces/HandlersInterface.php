<?php

/*
 * CWF-PHP Framework
 * 
 * File: Interfaces\HandlersInterface.php
 * Description: Error/Exception handlers interface.
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp\Interfaces;

interface HandlersInterface {

    public static function errorHandler(
            int $no,
            string $str,
            string $file,
            int $line): void;

    public static function exceptionHandler(\Throwable $ex): void;
    
}
