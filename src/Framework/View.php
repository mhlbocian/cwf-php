<?php

namespace Framework;

class View {

    private const VIEWDIR = APPDIR . DS . "Application" . DS . "Views";

    private string $view;
    private array $data = [];

    function __construct(string $view) {
        if (!file_exists(self::VIEWDIR . DS . "{$view}.php")) {
            throw new \Exception("View {$view} does not exist!");
        }
        
        $this->view = $view;
    }

    public function Bind(string $var, mixed $val): void {
        $this->data[$var] = $val;
    }

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
