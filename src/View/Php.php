<?php

/*
 * CWF-PHP Framework
 * 
 * File: View\Php.php
 * Description: View type - PHP file
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp\View;

use CwfPhp\CwfPhp\Interfaces\View\View_Object as IObject;

class Php implements IObject {

    private array $data = [];
    private string $file;

    #[\Override]
    public function Bind(string $var, mixed $value): Php {
        $this->data[$var] = $value;

        return $this;
    }

    #[\Override]
    public function Render(): string {
        \extract($this->data);
        \ob_start();

        try {
            include \APP_VIEWS . \DS . "{$this->file}.php";
        } catch (\Throwable $ex) {
            \ob_end_clean();

            throw $ex;
        }

        return \ob_get_clean();
    }

    #[\Override]
    public function __construct(string $file) {
        if (!\file_exists(\APP_VIEWS . \DS . "{$file}.php")) {

            throw new \Error("VIEW: '{$file}.php' does not exist");
        }

        $this->file = $file;
    }

    #[\Override]
    public function __toString(): string {

        return $this->Render();
    }
}
