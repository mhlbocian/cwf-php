<?php

/*
 * CWF-PHP Framework
 * 
 * File: View.php
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp;

use CwfPhp\CwfPhp\View\ViewType;
use CwfPhp\CwfPhp\Interfaces\ViewInterface;
use CwfPhp\CwfPhp\Interfaces\View\ObjectInterface;

/**
 * View class for handling HTML and PHP templates
 */
final class View implements ViewInterface {

    private ObjectInterface $view;

    #[\Override]
    public function __construct(string $view, ViewType $type = ViewType::PHP) {
        try {
            $this->view = match ($type) {
                ViewType::PHP => new View\Php($view),
                ViewType::HTML => new View\Html($view)
            };
        } catch (\UnhandledMatchError) {
            
            throw new \Error("VIEW: unknown view type");
        }
    }

    #[\Override]
    public function bind(string $var, mixed $val): View {
        $this->view->bind($var, $val);

        return $this;
    }

    private function render(): string {

        return $this->view->render();
    }

    #[\Override]
    public function __toString(): string {

        return $this->render();
    }
}
