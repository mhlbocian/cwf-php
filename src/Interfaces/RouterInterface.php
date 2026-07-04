<?php

/*
 * CWF-PHP Framework
 * 
 * File: Interfaces\RouterInterface.php
 * Description: Router interface
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp\Interfaces;

interface RouterInterface {

    public function __construct(?string $route);

    public function execute(): void;

    public static function getArgs(bool $withEnv): array;
}
