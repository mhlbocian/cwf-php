<?php

namespace Application\Controllers;

use Framework\View;
use Framework\Config;

class Main {

    private View $main;

    public function __construct() {
        $this->main = new View("main");
    }

    public function Index(...$args): void {
        $content = new View("Hello");
        $this->main->Bind("title", "Strona główna");
        $this->main->Bind("content", "Index");
    }

    public function About(...$args): void {
        $this->main->Bind("title", "O nas");
        $this->main->Bind("content", "Test");
    }

    public function __destruct() {
        echo $this->main;
    }
}
