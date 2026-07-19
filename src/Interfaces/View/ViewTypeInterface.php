<?php

/*
 * CWF-PHP Framework
 * 
 * File: Interfaces/View/ViewTypeInterface.php
 * Description: View object interface
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp\Interfaces\View;

interface ViewTypeInterface {

    public function __construct(string $file);

    public function bind(string $var, mixed $value): ObjectInterface;

    public function render(): string;

    public function __toString(): string;
}
