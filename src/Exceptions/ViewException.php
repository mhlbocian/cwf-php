<?php

namespace CwfPhp\CwfPhp\Exceptions;

class ViewException extends \Exception {

    public function __construct(string $view, string $message = "") {
        parent::__construct("Error while processing '{$view}'. {$message}");
    }
}
