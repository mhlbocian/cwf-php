<?php

/*
 * CWF-PHP Framework
 * 
 * File: bootstrap.php
 * Description: Bootstrap CWF-PHP Framework
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

declare(strict_types=1);
// define directories constants
define("DS", DIRECTORY_SEPARATOR);
define("ROOTDIR", __DIR__);
define("APPDIR", ROOTDIR . DS . "Application");
define("CFGDIR", ROOTDIR . DS . "Config");
define("DATADIR", ROOTDIR . DS . "Data");
// initialize basic functions and handlers
require_once ROOTDIR . DS . "Framework" . DS . "init.php";
// initialize framework and setup environment
Framework\Core::Init();
session_start();
