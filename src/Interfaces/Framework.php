<?php

/*
 * CWF-PHP Framework
 * 
 * File: Interfaces\Framework.php
 * Description: Framework interface
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp\Interfaces;

interface Framework {

    public function __construct(string $application_path);

    public static function Setup(string $application_path): void;

    public static function Error_Handler(
            int $no,
            string $str,
            string $file,
            int $line): void;
    
    public static function Exception_Handler(\Throwable $ex): void;
}
