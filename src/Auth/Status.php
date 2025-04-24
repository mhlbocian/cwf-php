<?php

/*
 * CWF-PHP Framework
 * 
 * File: Auth\Status.php
 * Description: Auth API - status codes
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Mhlbocian\CwfPhp\Auth;

enum Status {

    case EXISTS;
    case FAILED;
    case INVALID_INPUT;
    case NOTEXISTS;
    case SUCCESS;
}
