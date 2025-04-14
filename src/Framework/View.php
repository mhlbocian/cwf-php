<?php

/*
 * CWF-PHP Framework
 * 
 * File: View.php
 * Description: Views support
 * Author: Michał Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework;

use Exception;

class View {

    private const VIEWDIR = APPDIR . DS . "Views";

    /**
     * 
     * @var array Array of bind variables
     */
    private array $data = [];
    
    /**
     * 
     * @var string View content
     */
    private string $view;

    /**
     * Load view
     * 
     * @param string $view
     * @throws Exception
     */
    function __construct(string $view) {
        if (!file_exists(self::VIEWDIR . DS . "{$view}.php")) {

            throw new Exception("VIEW: '{$view}' does not exist");
        }

        $this->view = $view;
    }

    /**
     * Bind variable to view
     * 
     * @param string $var
     * @param mixed $val
     * @return void
     */
    public function Bind(string $var, mixed $val): void {
        $this->data[$var] = $val;
    }

    /**
     * Render view
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
