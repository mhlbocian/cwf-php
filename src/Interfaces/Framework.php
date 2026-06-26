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

    public static function App_Init(string $application_path): void;

    public static function Get_Env(?string $key): array;

    public static function Set_Directory(string $type, string $dirname): void;

    public static function Set_Env(string $key, mixed $value): void;
}
