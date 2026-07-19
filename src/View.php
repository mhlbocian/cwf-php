<?php

/*
 * CWF-PHP Framework
 * 
 * File: View.php
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp;

use CwfPhp\CwfPhp\Exceptions\ViewException;
use CwfPhp\CwfPhp\View\ViewType;
use CwfPhp\CwfPhp\Interfaces\ViewInterface;
use CwfPhp\CwfPhp\Interfaces\View\ViewTypeInterface;

/**
 * View class for handling HTML and PHP templates
 */
final class View implements ViewInterface {

    private ViewTypeInterface $view;

    /**
     * Load view and specify the type.ObjectInterface
     * 
     * Supported types: HTML, PHP
     * 
     * @param string $view View name (without extension)
     * @param ViewType $type Type of view (ViewType enum)
     * @throws \Error
     */
    #[\Override]
    public function __construct(string $view, ViewType $type = ViewType::PHP) {
        try {
            $this->view = match ($type) {
                ViewType::PHP => new View\Php($view),
                ViewType::HTML => new View\Html($view)
            };
        } catch (\UnhandledMatchError) {

            throw new ViewException($view, "Unknown view type");
        }
    }

    /**
     * Bind the in-template variable with the values
     * 
     * @param string $var Name of the variable
     * @param mixed $val Values to be binded with template value
     * @return View
     */
    #[\Override]
    public function bind(string $var, mixed $val): View {
        $this->view->bind($var, $val);

        return $this;
    }

    /**
     * Render (to string) the view
     * 
     * @return string
     */
    #[\Override]
    public function __toString(): string {

        return $this->view->render();
    }
}
