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

use Framework\Config;
use Framework\Url;

// initialize application info constants
define("APPNAME", Config::Get("application")["name"]);
define("APPDES", Config::Get("application")["description"]);
define("APPVER", Config::Get("application")["version"]);

// configure URL class
Url::Configure();

// start session
session_start();
