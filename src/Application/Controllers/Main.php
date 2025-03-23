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
        $view = new View("Main.Index");
        $this->main->Bind("title", "Strona główna");
        $this->main->Bind("page", "Index Page");
        $this->main->Bind("content", $view);
        $this->main->Bind("link", "/Main/About");
    }

    public function About(...$args): void {
        $view = new View("Main.About");
        $view->Bind("cfg_value", Config::Get("change", "test"));
        $this->main->Bind("title", "O nas");
        $this->main->Bind("page", "About Page");
        $this->main->Bind("content", $view);
        $this->main->Bind("link", "/Main/Index");
    }

    public function __destruct() {
        echo $this->main;
    }
}
