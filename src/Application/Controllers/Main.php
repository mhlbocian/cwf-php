<?php

namespace Application\Controllers;

use Framework\Router;
use Framework\View;
use Application\Models\Sitemap;

class Main {

    private View $main;

    public function __construct() {
        $this->main = new View("Main");
        $this->main->Bind("menu", Sitemap::Menu(Router::Current()));
        $this->main->Bind("title", Sitemap::Title(Router::Current()));
    }

    public function Index(...$args): void {
        $view = new View("Main.Index");
        $this->main->Bind("content", $view);
    }

    public function About(...$args): void {
        $view = new View("Main.About");
        $this->main->Bind("content", $view);
    }

    public function Examples(...$args): void {
        $view = new View("Main.Examples");
        $this->main->Bind("content", $view);
    }

    public function __destruct() {
        echo $this->main;
    }
}
