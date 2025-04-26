<?php

/*
 * CWF-PHP Framework
 * 
 * File: Interfaces\Url.php
 * Description: Url interface
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp\Interfaces;

interface Url {

    public static function Setup(): void;
    
    public static function Resource(string $path): string;

    public static function Site(string $path = ""): string;

    public static function Redirect(string $path = ""): void;
}
