<?php

/*
 * CWF-PHP Framework
 * 
 * File: DOCROOT/index.php
 * Description: Main index file
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

require_once '../bootstrap.php';

use Framework\{
    Exceptions\Router_Exception,
    Router,
    Url
};

// TODO: Filter PATH_INFO only for alphanumeric and slash chars
// [!] NOW IT'S VERY UNSAFE AND USED ONLY FOR EARLY DEVELOPMENT

$router = new Router($_SERVER["PATH_INFO"] ?? null);

try {
    $router->Execute();
} catch (Router_Exception $ex) {
// action for invalid route
    Url::Redirect();
}
