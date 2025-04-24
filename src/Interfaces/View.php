<?php

/*
 * CWF-PHP Framework
 * 
 * File: Interfaces\View.php
 * Description: View interface
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Mhlbocian\CwfPhp\Interfaces;

interface View {

    function __construct(string $view);

    public function Bind(string $var, mixed $val): void;

    public function __toString(): string;
}
