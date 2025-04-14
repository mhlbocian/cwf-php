<?php

namespace Application\Controllers;

use Framework\Router;
use Framework\View;
use Application\Models\Sitemap;

class Main {

    private View $main;

    public function __construct() {
        $this->main = new View("Main");
        $this->main->Bind("menu", Sitemap::Menu(Router::Get_Route()));
        $this->main->Bind("title", Sitemap::Title(Router::Get_Route()));
    }

    public function Index(): void {
        $view = new View("Main.Index");
        $this->main->Bind("content", $view);
    }

    public function License(): void {
        $view = new View("Main.License");
        $this->main->Bind("content", $view);
    }

    public function Examples(): void {
        $view = new View("Main.Examples");
        $this->main->Bind("content", $view);
    }
    
    public function Authentication(): void {
        $view = new View("Main.Authentication");
        $this->main->Bind("content", $view);
    }

    public function __destruct() {
        echo $this->main;
    }
}
