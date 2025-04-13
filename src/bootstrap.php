<?php

/*
 * CWF-PHP Framework
 * 
 * File: bootstrap.php
 * Description: Bootstrap CWF-PHP Framework
 * Author: Michał Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */
// define directories constants
define("DS", DIRECTORY_SEPARATOR);
define("ROOTDIR", __DIR__);
define("APPDIR", ROOTDIR . DS . "Application");
define("CFGDIR", ROOTDIR . DS . "Config");
define("DATADIR", ROOTDIR . DS . "Data");
// initialize basic functions and handlers
require_once ROOTDIR . DS . "Framework" . DS . "init.php";

use Framework\Auth;
use Framework\Config;
use Framework\Core;
use Framework\Url;

// configure core classes
Core::Check_Env();
Auth::Init();
Url::Init();
// initialize application info constants
define("APPNAME", Config::Get("application", "application")["name"]);
define("APPDES", Config::Get("application", "application")["description"]);
define("APPVER", Config::Get("application", "application")["version"]);
// start session
session_start();
