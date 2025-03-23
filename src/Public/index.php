<?php

/*
 * Custom Application Framework
 * 
 * Author: Michał Bocian <mhl.bocian@gmail.com>
 * License: 3-clause BSD
 */

use Framework\Config;
use Framework\Router;
use Framework\RouterException;

require_once '../bootstrap.php';

// TODO: Filter PATH_INFO only for alphanumeric and slash chars
// [!] NOW IT'S VERY UNSAFE AND USED ONLY FOR EARLY DEVELOPMENT

$router = new Router($_SERVER["PATH_INFO"] ?? null);

try {
    $router->Execute();
} catch (RouterException $ex) {
    // action for invalid route
    header("Location: /");
    exit();
}
