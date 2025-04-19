<?php

/*
 * CWF-PHP Framework
 * 
 * File: Framework\Interfaces\Data_Json.php
 * Description: Interface for JSON files (used in Config, and Data\Json)
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Interfaces;

interface Data_Json {

    public static function Exists(string $file): bool;

    public static function Fetch(string $file): array;

    public static function Get(string $file, string $key): mixed;

    public static function Set(string $file, string $key, mixed $value): void;
    
    public static function Unset(string $file, string $key): void;

    public static function Update(string $file): void;
}
