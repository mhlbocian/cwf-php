<?php

/*
 * CWF-PHP Framework
 * 
 * File: Interfaces\View.php
 * Description: View interface
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp\Interfaces;

interface View {

    function __construct(string $view);

    public function Bind(string $var, mixed $val): View;

    public function __toString(): string;
}
