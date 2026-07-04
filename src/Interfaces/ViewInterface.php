<?php

/*
 * CWF-PHP Framework
 * 
 * File: Interfaces\ViewInterface.php
 * Description: View interface
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp\Interfaces;

interface ViewInterface {

    function __construct(string $view);

    public function bind(string $var, mixed $val): ViewInterface;

    public function __toString(): string;
}
