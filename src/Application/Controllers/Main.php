<?php

namespace Application\Controllers;

use Framework\Router;
use Framework\View;
use Application\Models\Sitemap;
use Framework\Data\Json;

class Main {

    private View $main;

    public function __construct() {
        $this->main = new View("Main");
        $this->main->Bind("menu", Sitemap::MainMenu(Router::Get_Route()));
        $this->main->Bind("title", Sitemap::Title(Router::Get_Route()));
    }

    public function Index(): void {
        $view = new View("Main/Index");
        $this->main->Bind("content", $view);
    }

    public function API(): void {
        $page = Router::Get_Args()[0] ?? null;
        $view = new View("Main/API");

        $view->Bind("menu", Sitemap::ApiMenu($page));

        if ($page == null || !Sitemap::ApiSiteExists($page)) {
            $view->Bind("content", null);
        } else {
            $view->Bind("content", new View("API/{$page}"));
        }

        $this->main->Bind("content", $view);
    }

    public function License(): void {
        $view = new View("Main/License");
        $this->main->Bind("content", $view);
    }

    public function Usage(): void {
        $view = new View("Main/Usage");
        $this->main->Bind("content", $view);
    }

    public function __destruct() {
        echo $this->main;
    }
}
