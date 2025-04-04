<?php

namespace Application\Controllers;

use Framework\Router;
use Framework\View;
use Application\Models\Sitemap;

class Database {

    private View $main;

    public function __construct() {
        $this->main = new View("Main");
        $this->main->Bind("menu", Sitemap::Menu(Router::Get_Route()));
        $this->main->Bind("title", Sitemap::Title(Router::Get_Route()));
    }

    public function Index(): void {
        $view = new View("Database.Index");
        $page = Router::Get_Params()[0] ?? null;

        switch (in_array($page, range(1, 3))) {
            case true:
                $view->Bind("subpage", new View("Database.{$page}"));
                break;
            default:
                $view->Bind("subpage", "");
                break;
        }

        $this->main->Bind("content", $view);
    }

    public function __destruct() {
        echo $this->main;
    }
}
