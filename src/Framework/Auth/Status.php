<?php

/*
 * CWF-PHP Framework
 * 
 * File: Framework\Auth\Auth.php
 * Description: Auth API - status codes
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Auth;

enum Status {

    case EXISTS;
    case FAILED;
    case INVALID_INPUT;
    case SUCCESS;
}
