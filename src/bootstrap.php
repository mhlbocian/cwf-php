<?php

define("APPDIR", __DIR__);
define("DS", DIRECTORY_SEPARATOR);

require_once APPDIR . DS . "Framework" . DS . "functions.php";

set_error_handler("app_error_handler");
set_exception_handler("app_exception_handler");
spl_autoload_register("app_autoload_function");

use Framework\Config;

define("APPNAME", Config::Get("application")["name"]);
define("APPDES", Config::Get("application")["description"]);
define("APPVER", Config::Get("application")["version"]);

session_start();
// that's all folks!