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

    public function Authentication(): void {
        $view = new View("Main.Authentication");
        $this->main->Bind("content", $view);
    }

    public function Database(): void {
        $view = new View("Main.Database");
        $page = Router::Get_Args()[0] ?? null;

        switch (in_array($page, range(1, 3))) {
            case true:
                $view->Bind("subpage", new View("Database.{$page}"));
                break;
            default:
                $view->Bind("subpage", "<p><b>Select subpage</b></p>");
                break;
        }

        $this->main->Bind("content", $view);
    }

    public function License(): void {
        $view = new View("Main.License");
        $this->main->Bind("content", $view);
    }

    public function Usage(): void {
        $view = new View("Main.Usage");
        $this->main->Bind("content", $view);
    }

    public function __destruct() {
        echo $this->main;
    }
}
