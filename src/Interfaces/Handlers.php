<?php

/*
 * CWF-PHP Framework
 * 
 * File: Handlers.php
 * Description: Error/Exception handlers interface.
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp\Interfaces;

interface Handlers {

    public static function Error_Handler(
            int $no,
            string $str,
            string $file,
            int $line): void;

    public static function Exception_Handler(\Throwable $ex): void;
    
}
