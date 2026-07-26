<?php

namespace CwfPhp\CwfPhp\Exceptions;

class FrameworkException extends \Exception {

    public function __construct(string $module, string $message) {
        $exmsg = "Execution fault in module [" . \strtoupper($module) . "]: ";
        $exmsg .= $message;
        
        parent::__construct($exmsg);
    }
}
