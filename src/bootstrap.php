<?php

/*
 * CWF-PHP Framework
 * 
 * File: bootstrap.php
 * Description: Bootstrap CWF-PHP Framework
 * Author: Michał Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

define("APPDIR", __DIR__);
define("DS", DIRECTORY_SEPARATOR);

require_once APPDIR . DS . "Framework" . DS . "init.php";

use Framework\Config;
use Framework\Url;

define("APPNAME", Config::Get("application")["name"]);
define("APPDES", Config::Get("application")["description"]);
define("APPVER", Config::Get("application")["version"]);

Url::Configure();
session_start();
