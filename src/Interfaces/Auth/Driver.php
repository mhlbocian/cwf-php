<?php

/*
 * CWF-PHP Framework
 * 
 * File: Interfaces\Auth\Driver.php
 * Description: Auth API - driver interface
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Mhlbocian\CwfPhp\Interfaces\Auth;

interface Driver extends Common {

    public function __construct(array $auth_config);
}
