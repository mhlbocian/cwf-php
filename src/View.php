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

    private const string VIEWDIR = \APPDIR . \DS . "Views";
    
    private array $data = [];
    private string $view;
    
    #[\Override]
            function __construct(string $view) {
        if (!\file_exists(self::VIEWDIR . DS . "{$view}.php")) {

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
            include self::VIEWDIR . \DS . "{$this->view}.php";
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
