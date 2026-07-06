<?php

/*
 * CWF-PHP Framework
 * 
 * File: Interfaces\UrlInterface.php
 * Description: Url interface
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp\Interfaces;

interface UrlInterface {

    public static function base(bool $withPath = false): string;
    
    public static function redirect(?string $path = null): void;

    public static function resource(string $path): string;

    public static function site(?string $path = null): string;
}
