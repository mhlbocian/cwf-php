<?php

namespace Application\Controllers;

use Framework\{
    Auth,
    Url,
    Router,
    Database,
    Query,
    View
};
use Framework\Query\Statement;

class Authenticate {

    private Database $conn;
    private View $view;

    public function __construct() {
        $this->conn = new Database();
        $this->view = new View("Authenticate");
        $sites = [
            "CreateSchema" => "Create schema",
            "AddUser" => "Add user",
            "AddGroup" => "Add group",
            "Auth" => "Authenticate user",
            "Exists" => "Check existance of user or group"
        ];
        $this->view->Bind("sites", $sites);
    }

    public function Index(): void {
        $cnt = "<b>Users:</b>";
        $cnt .= "<ul>";

        foreach (Auth::GetUsers() as $username => $fullname) {
            $cnt .= "<li>{$username} ({$fullname})</li>";
        }

        $cnt .= "</ul>";
        $cnt .= "<b>Groups:</b>";
        $cnt .= "<ul>";

        foreach (Auth::GetGroups() as $groupname => $description) {
            $cnt .= "<li>{$groupname} ({$description})</li>";
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

        $cnt = "Adding user: <b>{$username}</b> with password: <b>{$password}</b>";

        $query = (new Query(Statement::INSERT))
                ->Table("users")
                ->Columns("username", "fullname", "password")
                ->Values($username)
                ->Values("Default Name")
                ->Values(password_hash($password, PASSWORD_DEFAULT));
        $cnt .= "<br>{$query}";
        $this->conn->Query($query);
        $cnt .= "<br>ok!";
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

        $cnt = "Adding group with name: <b>{$groupname}</b> and description: <b>{$description}</b>";
        $query = (new Query(Statement::INSERT))
                ->Table("groups")
                ->Columns("groupname", "description")
                ->Values($groupname, $description);
        $cnt .= "<br>{$query}";
        $this->conn->Query($query);
        $cnt .= "<br>ok!";
        $this->view->Bind("content", $cnt);
    }

    public function Auth(): void {
        $username = Router::Get_Args()[0] ?? null;
        $password = Router::Get_Args()[1] ?? null;

        if ($username == null || $password == null) {
            $cnt = "<b>You must specify username and password</b>";
            $this->view->Bind("content", $cnt);

            return;
        }

        $cnt = "User: <b>{$username}</b>. Auth: <b>";
        $cnt .= match (Auth::AuthUser($username, $password)) {
                    true => "passed!",
                    false => "failed!"
                } . "</b>";
        $this->view->Bind("content", $cnt);
    }

    public function Exists(): void {
        $type = Router::Get_Args()[0] ?? null;
        $name = Router::Get_Args()[1] ?? null;

        if ($type == null || $name == null) {
            $cnt = "<b>Specify action (user/group) and parameter (name)</b>";
            $this->view->Bind("content", $cnt);

            return;
        }

        $type = strtolower($type);

        switch ($type) {
            case "user":
                $cnt = "User: <b>{$name}</b> ";
                $cnt .= match (Auth::UserExists($name)) {
                    true => "exists!",
                    false => "does not exist!"
                };
                break;
            case "group":
                $cnt = "Group: <b>{$name}</b> ";
                $cnt .= match (Auth::GroupExists($name)) {
                    true => "exists!",
                    false => "does not exist!"
                };
                break;
            default:
                $cnt = "<b>Unknown action</b>";
                break;
        }
        
        $this->view->Bind("content", $cnt);
    }

    public function CreateSchema(): void {
        // QUERY 1: users table
        $cnt = "<code>";
        $query = (new Query(Statement::CREATE))
                ->Table("users")
                ->IfNotExists()
                ->ColType("username", "TEXT NOT NULL")
                ->ColType("fullname", "TEXT NOT NULL")
                ->ColType("password", "TEXT NOT NULL")
                ->PrimaryKey("username");
        $cnt .= $query . "<br>QUERY OK.<br>";
        $this->conn->Query($query);
        // QUERY 2: groups table
        $query = (new Query(Statement::CREATE))
                ->Table("groups")
                ->IfNotExists()
                ->ColType("groupname", "TEXT NOT NULL")
                ->ColType("description", "TEXT NOT NULL")
                ->PrimaryKey("groupname");
        $cnt .= $query . "<br>QUERY OK.<br>";
        $this->conn->Query($query);
        // QUERY 3: membership table
        $query = (new Query(Statement::CREATE))
                ->Table("memberships")
                ->IfNotExists()
                ->ColType("username", "TEXT NOT NULL")
                ->ColType("groupname", "TEXT NOT NULL")
                ->ForeginKey("username", "users(username)")
                ->ForeginKey("groupname", "groups(groupname)");
        $cnt .= $query . "<br>QUERY OK.</code>";
        $this->conn->Query($query);
        $this->view->Bind("content", $cnt);
    }

    public function __destruct() {
        echo $this->view;
    }
}
