<?php

/*
 * CWF-PHP Framework
 * 
 * File: Driver.php
 * Description: Authentication framework - Drivers
 * Author: Michał Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Auth;

enum Driver: string {

    // define drivers and method prefixes
    case Database = "Db";
    case LDAP = "Ldap";
}