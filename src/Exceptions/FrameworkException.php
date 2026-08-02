<?php

namespace CwfPhp\CwfPhp\Exceptions;

class FrameworkException extends \Exception {

    public function __construct(string $class, string $message) {

        parent::__construct("An error occured in the [{$class}]: {$message}");
    }
}
