<?php

/*
 * CWF-PHP Framework
 * 
 * File: Framework\init.php
 * Description: Core script. Define basic functions and setup handlers
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */
// not expected to be run beyond bootstrap
if (!defined("APPDIR")) {
    die();
}

/**
 * Return contents of file in `ROOTDIR/Static` directory
 * 
 * @param string $filename
 * @return string
 * @throws Error
 */
function static_getfile(string $filename): string {
    if (!file_exists($path = ROOTDIR . DS . "Static" . DS . "{$filename}")) {

        throw new Error("CORE: static file '{$path}' does not exist");
    }

    return file_get_contents($path);
}

/**
 * Simple template parser. Used only for framework-internal work, like showing
 * exceptions and errors. Templates are stored in `ROOTDIR/Static` directory
 * 
 * @param string $template
 * @param array $vars
 * @return string
 */
function static_template(string $template, array $vars = []): string {
    $cnt = static_getfile("{$template}.html");

    foreach ($vars as $var => $val) {
        $cnt = str_replace("{\$$var}", $val, $cnt);
    }

    return $cnt;
}

/**
 * Convert image file contents to string encoded in base64, which can be
 * included later directly in the document, as `ROOTDIR/Static` directory is
 * inaccessible for the web server
 * 
 * @param string $image
 * @param string $type
 * @return string
 */
function static_imgb64(string $image, string $type): string {
    $cnt = static_getfile("{$image}.{$type}");
    $hdr = "data:image/{$type};base64,";

    return $hdr . base64_encode($cnt);
}

/**
 * Error handler for CWF-PHP
 * 
 * @param int $no
 * @param string $str
 * @param string $file
 * @param int $line
 * @return void
 */
function app_error_handler(int $no, string $str, string $file, int $line): void {
    echo static_template("error", [
        "type" => "Error",
        "no" => $no,
        "file" => $file,
        "line" => $line,
        "message" => $str,
        "image" => static_imgb64("error", "png")
    ]);
}

/**
 * Exception handler for CWF-PHP
 * 
 * @param Throwable $ex
 * @return void
 */
function app_exception_handler(Throwable $ex): void {
    echo static_template("error", [
        "type" => $ex::class,
        "no" => $ex->getCode(),
        "file" => $ex->getFile(),
        "line" => $ex->getLine(),
        "message" => $ex->getMessage() . "<br/><br/>" . $ex->getTraceAsString(),
        "image" => static_imgb64("error", "png")
    ]);
}

/**
 * Auto-loader for CWF-PHP
 * 
 * @param string $class
 * @return void
 */
function app_autoload_function(string $class): void {
    $file = ROOTDIR . DS . str_replace("\\", DS, $class) . ".php";

    if (!file_exists($file)) {

        return;
    }

    require_once $file;
}

// setup handlers
set_error_handler("app_error_handler");
set_exception_handler("app_exception_handler");
spl_autoload_register("app_autoload_function");
