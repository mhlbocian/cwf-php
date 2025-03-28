<?php

namespace Application\Controllers;

use Framework\Router;
use Framework\View;
use Framework\Database\Connection;
use Framework\Database\Query;
use Framework\Database\Operation;
use Application\Models\Sitemap;

class Database {

    private View $main;

    public function __construct() {
        $this->main = new View("Main");
        $this->main->Bind("menu", Sitemap::Menu(Router::Current()));
        $this->main->Bind("title", Sitemap::Title(Router::Current()));
    }

    public function Index(...$args): void {
        $view = new View("Database.Index");

        switch (in_array($args[0] ?? 0, range(1, 3))) {
            case true:
                $view->Bind("subpage", new View("Database.{$args[0]}"));
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
