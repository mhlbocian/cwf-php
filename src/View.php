<?php

/*
 * CWF-PHP Framework
 * 
 * File: View.php
 * Description: Views class
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp;

final class View implements Interfaces\View {

    private array $data = [];
    private string $view;

    #[\Override]
    public function __construct(string $view) {
        if (!\file_exists(\APP_VIEWS . \DS . "{$view}.php")) {

            throw new \Exception("VIEW: '{$view}' does not exist");
        }

        $this->view = $view;
    }

    #[\Override]
    public function Bind(string $var, mixed $val): View {
        $this->data[$var] = $val;

        return $this;
    }

    private function Render(): string {
        \extract($this->data);
        \ob_start();

        try {
            include \APP_VIEWS . \DS . "{$this->view}.php";
        } catch (\Exception $ex) {
            \ob_end_clean();

            throw $ex;
        }

        return \ob_get_clean();
    }

    #[\Override]
    public function __toString(): string {
        return $this->Render();
    }
}
