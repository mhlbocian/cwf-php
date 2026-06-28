<?php

/*
 * CWF-PHP Framework
 * 
 * File: Interfaces/View/View_Object.php
 * Description: View object interface
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp\Interfaces\View;

interface View_Object {

    public function __construct(string $file);

    public function Bind(string $var, mixed $value): Object;

    public function Render(): string;

    public function __toString(): string;
}
