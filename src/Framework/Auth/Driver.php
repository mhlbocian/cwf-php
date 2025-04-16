<?php

/*
 * CWF-PHP Framework
 * 
 * File: Framework\Auth\Auth.php
 * Description: Auth API - drivers
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Auth;

enum Driver: string {

    // define drivers and method prefixes
    case Database = "Db";
    case LDAP = "Ldap";
}