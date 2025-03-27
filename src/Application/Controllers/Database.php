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
        $db = new Connection();
        $content = "<pre>";
        
        $query = (new Query(Operation::SELECT))
                ->Table("test")
                ->Columns("id", "username")
                ->OrderBy("id")
                ->OrderBy("username", false)
                ->Limit(10)
                ->Offset(100);
        
        $content .= $query . "<br />";
        
        $query = (new Query(Operation::CREATE))
                ->Table("test")
                ->Colspec("id", "integer")
                ->Colspec("username", "text")
                ->Colspec("password", "text");
        
        $content .= $query;
        
        $content .= "</pre>";
        $this->main->Bind("content", $content);
    }

    public function __destruct() {
        echo $this->main;
    }
}
