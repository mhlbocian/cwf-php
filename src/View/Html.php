<?php

/*
 * CWF-PHP Framework
 * 
 * File: View\Html.php
 * Description: View type - HTML file
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp\View;

use CwfPhp\CwfPhp\Exceptions\ViewException;
use CwfPhp\CwfPhp\Interfaces\View\ViewTypeInterface;

class Html implements ViewTypeInterface {

    private array $data = [];
    private string $file;

    #[\Override]
    public function bind(string $var, mixed $value): Html {
        $this->data[$var] = $value;

        return $this;
    }

    #[\Override]
    public function render(): string {
        foreach ($this->data as $var => $value) {
            $this->file = \str_replace("{\$$var}", $value, $this->file);
        }
        /** process the {% View "path/to/view" %} expression */
        $this->file = \preg_replace_callback(
                '/\{\%\s*View\s+"([^"]+)"\s*\%\}/',
                function ($matches) {
                    $file = $matches[1];

                    return new Html($file);
                },
                $this->file
        );

        return $this->file;
    }

    #[\Override]
    public function __construct(string $file) {
        $filepath = \APP_VIEWS . \DS . "{$file}.html";

        if (!\file_exists($filepath)) {

            throw new ViewException($file, "File not exists");
        }

        $this->file = \file_get_contents($filepath);
    }

    #[\Override]
    public function __toString(): string {

        return $this->render();
    }
}
