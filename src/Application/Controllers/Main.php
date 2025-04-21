<?php

namespace Application\Controllers;

use Framework\Auth;
use Framework\Router;
use Framework\Url;
use Framework\View;
use Application\Models\Sitemap;

class Main {

    private View $main;

    public function __construct() {
        $this->main = new View("Main");
        $menu = Sitemap::MainMenu(Router::Get_Route());
        
        $this->main->Bind("title", Sitemap::Title(Router::Get_Route()));

        if (Auth::IsLogged()) {
            // replace `Sign In` in menu array with `Logout {$user}`
            $fullname = Auth::Session()["fullname"];
            $key = array_search("Sign in", array_column($menu, "description"));
            $menu[$key]["description"] = "Logout ({$fullname})";
        } else {
            $this->main->Bind("login", null);
        }

        $this->main->Bind("menu", $menu);
    }

    public function Index(): void {
        $view = new View("Main/Index");
        
        $this->main->Bind("content", $view);
    }

    public function License(): void {
        $view = new View("Main/License");
        
        $this->main->Bind("content", $view);
    }

    public function Login(): void {
        if (Auth::IsLogged()) {
            Auth::Logout();
            Url::Redirect();
        }

        $view = new View("Main/Login");
        
        $view->Bind("status", Router::Get_Args()[0] ?? null);
        $this->main->Bind("content", $view);
    }
    
    public function Manual(): void {
        $page = Router::Get_Args()[0] ?? null;
        $view = new View("Main/Manual");

        $view->Bind("menu", Sitemap::ManualMenu($page));

        if ($page == null || !Sitemap::ManualExists($page)) {
            $view->Bind("content", null);
        } else {
            try {
                $view->Bind("content", new View("Manual/{$page}"));
            } catch (\Throwable) {
                $view->Bind("content", "<p>Work in progress</p>");
            }
        }

        $this->main->Bind("content", $view);
    }

    public function Usage(): void {
        $view = new View("Main/Usage");
        
        $this->main->Bind("content", $view);
    }

    public function UsersGroups(): void {
        $view = new View("Main/UsersGroups");
        
        $view->Bind("users", Auth::UserFetch());
        $view->Bind("groups", Auth::GroupFetch());
        $view->Bind("status", Router::Get_Args()[0] ?? null);
        $this->main->Bind("content", $view);
    }

    public function __destruct() {
        echo $this->main;
    }
}
