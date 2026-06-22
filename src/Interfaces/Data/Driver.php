<?php

/*
 * CWF-PHP Framework
 * 
 * File: Interfaces\Data\Driver.php
 * Description: Data driver interface
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp\Interfaces\Data;

interface Driver {

    public function __construct(string $file);
    
    public function Clear(): void;
    
    public function Create(): void;
    
    public function Exists(): bool;

    public function Fetch(): array;

    public function Get(string $key): mixed;

    public function Set(string $key, mixed $value): void;

    public function Unset(string $key): void;
}
