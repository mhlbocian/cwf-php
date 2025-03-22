<?php

use Framework\Config;
use Framework\Router;
use Framework\RouterException;

require_once '../bootstrap.php';

$Router = new Router($_SERVER["PATH_INFO"] ?? null);

try {
    $Router->Execute();
} catch (RouterException $ex) {
    // action for invalid route
    header("Location: /");
    exit();
}
