<?php

namespace Application\Controllers;

use Framework\Auth;
use Framework\Url;
use Framework\Router;
use Framework\Database\{
    Connection,
    Query,
    Statement
};

class Authenticate {

    private Connection $conn;

    public function __construct() {
        $this->conn = new Connection();
        $sites = [
            "CreateSchema" => "Create schema for default config",
            "AddUser" => "Add user",
            "AddGroup" => "Add group",
            "Auth" => "Authenticate user",
            "Exists" => "Check existance of user or group"
        ];

        echo "<!doctype html><html><head><meta charset=\"utf-8\"/>";
        echo "<link rel=\"stylesheet\" href=\"" . Url::Site("css/style.css", false) . "\"/>";
        echo "<title>Authentication framework test suite</title></head>";
        echo "<div id=\"page-container\">";
        echo "<h1>Auth library tests</h1>";
        echo "<ul>";

        foreach ($sites as $site => $desc) {
            echo "<li><a href=\"" . Url::Site("Authenticate/" . $site) . "\">{$desc}</a></li>";
        }

        echo "</ul>";
        echo "<p>Sample use: ";
        echo Url::Site("Authenticate/AddUser/[username]/[password]");
        echo "</p><hr/>";
    }

    public function Index(): void {
        echo "<b>Users:</b>";
        echo "<ul>";

        foreach (Auth::GetUsers() as $username => $fullname) {
            echo "<li>{$username} ({$fullname})</li>";
        }

        echo "</ul>";
        echo "<b>Groups:</b>";
        echo "<ul>";

        foreach (Auth::GetGroups() as $groupname => $description) {
            echo "<li>{$groupname} ({$description})</li>";
        }

        echo "</ul>";
    }

    public function AddUser(): void {
        $username = Router::Get_Args()[0] ?? null;
        $password = Router::Get_Args()[1] ?? null;

        if ($username == null || $password == null) {
            echo "<b>Username and password required</b>";

            return;
        }

        echo "Adding user: <b>{$username}</b> with password: <b>{$password}</b>";

        $query = (new Query(Statement::INSERT))
                ->Table("users")
                ->Columns("username", "fullname", "password")
                ->Values($username)
                ->Values("Default Name")
                ->Values(password_hash($password, PASSWORD_DEFAULT));
        echo "<br>{$query}";
        $this->conn->Query($query);
        echo "<br>ok!";
    }

    public function AddGroup(): void {
        $groupname = Router::Get_Args()[0] ?? null;
        $description = Router::Get_Args()[1] ?? null;

        if ($groupname == null || $description == null) {
            echo "<b>Name and description required</b>";

            return;
        }

        echo "Adding group with name: <b>{$groupname}</b> and description: <b>{$description}</b>";
        $query = (new Query(Statement::INSERT))
                ->Table("groups")
                ->Columns("groupname", "description")
                ->Values($groupname, $description);
        echo "<br>{$query}";
        $this->conn->Query($query);
    }

    public function Auth(): void {
        $username = Router::Get_Args()[0] ?? null;
        $password = Router::Get_Args()[1] ?? null;

        if ($username == null || $password == null) {
            echo "<b>You must specify username and password</b>";

            return;
        }

        echo "User: <b>{$username}</b>. Auth: <b>";
        echo match (Auth::AuthUser($username, $password)) {
            true => "passed!",
            false => "failed!"
        } . "</b>";
    }

    public function Exists(): void {
        $type = Router::Get_Args()[0] ?? null;
        $name = Router::Get_Args()[1] ?? null;

        if ($type == null || $name == null) {
            echo "<b>Specify action (user/group) and parameter (name)</b>";

            return;
        }

        $type = strtolower($type);

        switch ($type) {
            case "user":
                echo "User: <b>{$name}</b> ";
                echo match (Auth::UserExists($name)) {
                    true => "exists!",
                    false => "does not exist!"
                };
                break;
            case "group":
                echo "Group: <b>{$name}</b> ";
                echo match (Auth::GroupExists($name)) {
                    true => "exists!",
                    false => "does not exist!"
                };
                break;
            default:
                echo "<b>Unknown action</b>";
                break;
        }
    }

    public function CreateSchema(): void {
        // QUERY 1: users table
        echo "<code>";
        $query = (new Query(Statement::CREATE))
                ->Table("users")
                ->IfNotExists()
                ->Colspec("username", "TEXT NOT NULL")
                ->Colspec("fullname", "TEXT NOT NULL")
                ->Colspec("password", "TEXT NOT NULL")
                ->PrimaryKey("username");
        echo $query . "<br>";
        $this->conn->Query($query);
        // QUERY 2: groups table
        $query = (new Query(Statement::CREATE))
                ->Table("groups")
                ->IfNotExists()
                ->Colspec("groupname", "TEXT NOT NULL")
                ->Colspec("description", "TEXT NOT NULL")
                ->PrimaryKey("groupname");
        echo $query . "<br>";
        $this->conn->Query($query);
        // QUERY 3: membership table
        $query = (new Query(Statement::CREATE))
                ->Table("memberships")
                ->IfNotExists()
                ->Colspec("username", "TEXT NOT NULL")
                ->Colspec("groupname", "TEXT NOT NULL")
                ->ForeginKey("username", "users(username)")
                ->ForeginKey("groupname", "groups(groupname)");
        echo $query . "</code>";
        $this->conn->Query($query);
    }

    public function __destruct() {
        echo "<hr/><br><p style=\"text-align: right;\">";
        echo "<a href=\"" . Url::Site("Authenticate") . "\">Return to index</a>";
        echo "</p></div></body></html>";
    }
}
