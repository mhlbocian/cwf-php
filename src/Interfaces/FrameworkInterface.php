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

interface FrameworkInterface {

    public function __construct(string $appPath);

    public static function application(string $appPath): void;

    public static function getEnv(?string $key): array;

    public static function setDir(string $type, string $dirname): void;

    public static function setEnv(string $key, mixed $value): void;
}
