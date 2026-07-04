<?php

/*
 * CWF-PHP Framework
 * 
 * File: Interfaces\Data\ConfigFilesInterface.php
 * Description: Data driver interface for config files like json, ini
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp\Interfaces\Data;

interface ConfigFileInterface {

    public function __construct(string $file);
    
    public function clear(): void;
    
    public function create(): void;
    
    public function exists(): bool;

    public function fetch(): array;

    public function get(string $key): mixed;

    public function set(string $key, mixed $value): void;

    public function unset(string $key): void;
}
