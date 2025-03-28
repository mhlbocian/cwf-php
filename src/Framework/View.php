<?php

/*
 * CWF-PHP Framework
 * 
 * File: View.php
 * Description: Framework\View class
 * Author: Michał Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework;

use Exception;

class View {

    private const VIEWDIR = APPDIR . DS . "Views";

    private string $view;
    private array $data = [];

    /**
     * Loads view
     * 
     * @param string $view
     * @throws Exception
     */
    function __construct(string $view) {
        if (!file_exists(self::VIEWDIR . DS . "{$view}.php")) {
            throw new Exception("View {$view} does not exist");
        }

        $this->view = $view;
    }

    /**
     * Binds variables to view
     * 
     * @param string $var
     * @param mixed $val
     * @return void
     */
    public function Bind(string $var, mixed $val): void {
        $this->data[$var] = $val;
    }

    /**
     * Renders view
     * 
     * @return string
     * @throws Exception
     */
    private function Render(): string {
        extract($this->data);
        ob_start();
        try {
            include self::VIEWDIR . DS . "{$this->view}.php";
        } catch (Exception $ex) {
            ob_end_clean();
            throw $ex;
        }

        return ob_get_clean();
    }

    public function __toString(): string {
        return $this->Render();
    }
}
