<?php

/*
 * CWF-PHP Framework
 * 
 * File: Framework\Interfaces\Router.php
 * Description: Router interface
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Interfaces;

interface Router {

    public function __construct(?string $route);

    public function Execute(): void;

    public static function Get_Args(): array;

    public static function Get_Route(): string;
}
