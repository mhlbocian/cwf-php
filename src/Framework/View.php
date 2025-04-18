<?php

/*
 * CWF-PHP Framework
 * 
 * File: Framework\View.php
 * Description: Views class
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework;

final class View implements Interfaces\View {

    private const string VIEWDIR = \APPDIR . \DS . "Views";

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
    #[\Override]
            function __construct(string $view) {
        if (!\file_exists(self::VIEWDIR . DS . "{$view}.php")) {

            throw new \Exception("VIEW: '{$view}' does not exist");
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
    #[\Override]
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
        \extract($this->data);
        \ob_start();

        try {
            include self::VIEWDIR . \DS . "{$this->view}.php";
        } catch (\Exception $ex) {
            \ob_end_clean();

            throw $ex;
        }

        return \ob_get_clean();
    }

    /**
     * Return rendered view as string
     * 
     * @return string
     */
    #[\Override]
    public function __toString(): string {
        return $this->Render();
    }
}
