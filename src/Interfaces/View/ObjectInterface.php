<?php

/*
 * CWF-PHP Framework
 * 
 * File: Interfaces/View/ObjectInterface.php
 * Description: View object interface
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp\Interfaces\View;

interface ObjectInterface {

    public function __construct(string $file);

    public function bind(string $var, mixed $value): ObjectInterface;

    public function render(): string;

    public function __toString(): string;
}
