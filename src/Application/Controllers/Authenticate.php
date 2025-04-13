<?php

namespace Application\Controllers;

class Authenticate {

    private \Framework\Database\Connection $conn;

    public function __construct() {
        $this->conn = new \Framework\Database\Connection();
    }

    public function AddUser(): void {
        $username = \Framework\Router::Get_Args()[0] ?? null;
        $password = \Framework\Router::Get_Args()[1] ?? null;

        if ($username == null || $password == null) {
            echo "<b>Username and password required</b>";

            return;
        }

        echo "Adding user: <b>{$username}</b> with password: <b>{$password}</b>";

        $query = (new \Framework\Database\Query(\Framework\Database\Statement::INSERT))
                ->Table("users")
                ->Columns("username", "fullname", "password")
                ->Values($username)
                ->Values("Test User")
                ->Values(password_hash($password, PASSWORD_DEFAULT));
        echo "<br>{$query}";
        $this->conn->Query($query);
        echo "<br>ok!";
    }

    public function AddGroup(): void {
        $groupname = \Framework\Router::Get_Args()[0] ?? null;
        $description = \Framework\Router::Get_Args()[1] ?? null;

        if ($groupname == null || $description == null) {
            echo "<b>Name and description required</b>";

            return;
        }

        echo "Adding group with name: <b>{$groupname}</b> and description: <b>{$description}</b>";
        $query = (new \Framework\Database\Query(\Framework\Database\Statement::INSERT))
                ->Table("groups")
                ->Columns("groupname", "description")
                ->Values($groupname, $description);
        echo "<br>{$query}";
        $this->conn->Query($query);
    }

    public function AuthUser(): void {
        $username = \Framework\Router::Get_Args()[0] ?? null;
        $password = \Framework\Router::Get_Args()[1] ?? null;

        if ($username == null || $password == null) {
            echo "<b>You must specify username and password</b>";

            return;
        }

        echo "User: <b>{$username}</b>. Auth: <b>";
        echo match (\Framework\Auth::AuthUser($username, $password)) {
            true => "passed!",
            false => "failed!"
        } . "</b>";
    }

    public function Exists(): void {
        $type = \Framework\Router::Get_Args()[0] ?? null;
        $name = \Framework\Router::Get_Args()[1] ?? null;

        if ($type == null || $name == null) {
            echo "<b>Specify action (user/group) and parameter (name)</b>";

            return;
        }

        $type = strtolower($type);

        switch ($type) {
            case "user":
                echo "User: <b>{$name}</b> ";
                echo match (\Framework\Auth::UserExists($name)) {
                    true => "exists!",
                    false => "does not exist!"
                };
                break;
            case "group":
                echo "Group: <b>{$name}</b> ";
                echo match (\Framework\Auth::GroupExists($name)) {
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
        $query = (new \Framework\Database\Query(\Framework\Database\Statement::CREATE))
                ->Table("users")
                ->IfNotExists()
                ->Colspec("username", "TEXT NOT NULL")
                ->Colspec("fullname", "TEXT NOT NULL")
                ->Colspec("password", "TEXT NOT NULL")
                ->PrimaryKey("username");
        echo $query . "<br>";
        $this->conn->Query($query);
        // QUERY 2: groups table
        $query = (new \Framework\Database\Query(\Framework\Database\Statement::CREATE))
                ->Table("groups")
                ->IfNotExists()
                ->Colspec("groupname", "TEXT NOT NULL")
                ->Colspec("description", "TEXT NOT NULL")
                ->PrimaryKey("groupname");
        echo $query . "<br>";
        $this->conn->Query($query);
        // QUERY 3: membership table
        $query = (new \Framework\Database\Query(\Framework\Database\Statement::CREATE))
                ->Table("memberships")
                ->IfNotExists()
                ->Colspec("username", "TEXT NOT NULL")
                ->Colspec("groupname", "TEXT NOT NULL")
                ->ForeginKey("username", "users(username)")
                ->ForeginKey("groupname", "groups(groupname)");
        echo $query . "<br>";
        $this->conn->Query($query);
    }
}
