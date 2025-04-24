<?php

/*
 * CWF-PHP Framework
 * 
 * File: Interfaces\Data\Json.php
 * Description: Data interfaces - JSON
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Mhlbocian\CwfPhp\Interfaces\Data;

interface Json {

    public function __construct(string $file);

    public function Fetch(): array;

    public function Get(string $key): mixed;

    public function Set(string $key, mixed $value): void;

    public function Unset(string $key): void;
}
