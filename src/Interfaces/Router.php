<?php

/*
 * CWF-PHP Framework
 * 
 * File: Interfaces\Router.php
 * Description: Router interface
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp\Interfaces;

interface Router {

    public function __construct(?string $route);

    public function Execute(): void;

    public static function Get_Args(): array;

    public static function Get_Route(): string;
}
