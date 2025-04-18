<?php

namespace Application\Controllers;

use Framework\{
    Auth,
    Config,
    Database,
    Query,
    Query\Statement,
    Router,
    Url,
    View
};

class Authenticate {

    private Database $conn;
    private View $view;

    public function __construct() {
        $this->conn = new Database(Config::Get("authentication", "connection"));
        $this->view = new View("Authenticate");
        $sites = [
            "CreateSchema" => "Create schema",
            "AddUser" => "Add user",
            "AddGroup" => "Add group",
            "Login" => "Log in",
        ];

        if (Auth::IsLogged()) {
            $user = Auth::Session()["username"];
            $sites["Login"] = "Log out [{$user}]";
        }

        $this->view->Bind("sites", $sites);
    }

    public function Index(): void {
        $cnt = "<b>Users:</b>";
        $cnt .= "<ul>";

        foreach (Auth::UserFetch() as $username => $fullname) {
            $cnt .= "<li>{$username} ({$fullname})</li>";
        }

        $cnt .= "</ul>";
        $cnt .= "<b>Groups:</b>";
        $cnt .= "<ul>";

        foreach (Auth::GroupFetch() as $groupname => $description) {
            $cnt .= "<li>{$groupname} ({$description})";

            foreach (Auth::UserFetch($groupname) as $username => $fullname) {
                $cnt .= " [{$username} ({$fullname})]";
            }

            $cnt .= "</li>";
        }

        $cnt .= "</ul>";
        $this->view->Bind("content", $cnt);
    }

    public function AddUser(): void {
        $username = Router::Get_Args()[0] ?? null;
        $password = Router::Get_Args()[1] ?? null;

        if ($username == null || $password == null) {
            $cnt = "<b>Username and password required</b>";
            $this->view->Bind("content", $cnt);

            return;
        }

        $cnt = "Adding user: <b>{$username}</b> with password: <b>{$password}</b> [";
        $cnt .= match (Auth::UserAdd($username, "AbuRebu", $password)) {
            Auth\Status::FAILED => "failed",
            Auth\Status::INVALID_INPUT => "invalid input data",
            Auth\Status::SUCCESS => "success",
            Auth\Status::EXISTS => "user already exists"
        };
        $cnt .= "]";

        $this->view->Bind("content", $cnt);
    }

    public function AddGroup(): void {
        $groupname = Router::Get_Args()[0] ?? null;
        $description = Router::Get_Args()[1] ?? null;

        if ($groupname == null || $description == null) {
            $cnt = "<b>Name and description required</b>";
            $this->view->Bind("content", $cnt);

            return;
        }

        $cnt = "Adding group: <b>{$groupname}</b> <i>({$description})</i> [";
        $cnt .= match (Auth::GroupAdd($groupname, $description)) {
            Auth\Status::EXISTS => "group exists",
            Auth\Status::FAILED => "failed",
            Auth\Status::SUCCESS => "success"
        };
        $cnt .= "]";
        $this->view->Bind("content", $cnt);
    }

    public function CreateSchema(): void {
        // QUERY 1: users table
        $cnt = "<code>";
        $query = (new Query(Statement::CREATE))
                ->Table("users")
                ->IfNotExists()
                ->ColType("username", "varchar(255) not null")
                ->ColType("fullname", "varchar(255) not null")
                ->ColType("password", "varchar(255) not null")
                ->PrimaryKey("username");
        $cnt .= $query . "<br>QUERY OK.<br>";
        $this->conn->Query($query);
        // QUERY 2: groups table
        $query = (new Query(Statement::CREATE))
                ->Table("groups")
                ->IfNotExists()
                ->ColType("groupname", "varchar(255) not null")
                ->ColType("description", "varchar(255) not null")
                ->PrimaryKey("groupname");
        $cnt .= $query . "<br>QUERY OK.<br>";
        $this->conn->Query($query);
        // QUERY 3: membership table
        $query = (new Query(Statement::CREATE))
                ->Table("memberships")
                ->IfNotExists()
                ->ColType("username", "varchar(255) not null")
                ->ColType("groupname", "varchar(255) not null")
                ->ForeginKey("username", "users(username)")
                ->ForeginKey("groupname", "groups(groupname)")
                ->Unique("username", "groupname");
        $cnt .= $query . "<br>QUERY OK.</code>";
        $this->conn->Query($query);
        $this->view->Bind("content", $cnt);
    }

    public function Login(): void {
        if (Auth::IsLogged()) {
            Auth::Logout();
            Url::Redirect("Authenticate");
        }

        $username = Router::Get_Args()[0] ?? null;
        $password = Router::Get_Args()[1] ?? null;

        if ($username == null || $password == null) {
            $cnt = "<b>You must specify username and password</b>";
            $this->view->Bind("content", $cnt);

            return;
        }

        $auth = Auth::Login($username, $password);

        if ($auth == Auth\Status::SUCCESS) {
            Url::Redirect("Authenticate");
        }

        $cnt = "User: <b>{$username}</b>. Auth: <b>";
        $cnt .= match ($auth) {
                    Auth\Status::FAILED => "failed",
                } . "</b>";
        $this->view->Bind("content", $cnt);
    }

    public function __destruct() {
        echo $this->view;
    }
}
