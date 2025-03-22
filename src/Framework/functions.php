<?php

if (!defined("APPDIR"))
    die();

function static_getfile(string $filename): string {
    if (!file_exists($path = APPDIR . DS . "Static" . DS . "{$filename}")) {
        throw new Error("Static file {$path} does not exist!");
    }

    return file_get_contents($path);
}

function static_template(string $template, array $vars = []): string {
    $cnt = static_getfile("{$template}.html");
    foreach ($vars as $var => $val) {
        $cnt = str_replace("{\$$var}", $val, $cnt);
    }

    return $cnt;
}

function static_imgb64(string $image, string $type): string {
    $cnt = static_getfile("{$image}.{$type}");
    $hdr = "data:image/{$type};base64,";

    return $hdr . base64_encode($cnt);
}

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

function app_exception_handler(Throwable $ex): void {
    echo static_template("error", [
        "type" => "Exception",
        "no" => $ex->getCode(),
        "file" => $ex->getFile(),
        "line" => $ex->getLine(),
        "message" => $ex->getMessage() . "<br/><br/>" . $ex->getTraceAsString(),
        "image" => static_imgb64("error", "png")
    ]);
}

function app_autoload_function(string $class): void {
    $file = APPDIR . DS . str_replace("\\", DS, $class) . ".php";

    if (!file_exists($file)) {
        return;
    }

    require_once $file;
}
