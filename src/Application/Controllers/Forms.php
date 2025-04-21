<?php

namespace Application\Controllers;

use Framework\Auth;
use Framework\Auth\Status;
use Framework\Url;

class Forms {

    public function __construct() {
        if (empty($_POST)) {
            Url::Redirect();
        }
    }

    public function AddGroup(): void {
        $groupname = $_POST["inpGroupname"] ?? "";
        $description = $_POST["inpDescription"] ?? "";

        $status = Auth::GroupAdd($groupname, $description);

        switch ($status) {
            case Status::EXISTS:
                Url::Redirect("/Main/UsersGroups/groupexists");
                break;
            case Status::SUCCESS:
                Url::Redirect("/Main/UsersGroups/groupsuccess");
                break;
            default:
                Url::Redirect("/Main/UsersGroups/groupfailed");
        }
    }

    public function AddUser(): void {
        $username = $_POST["inpUsername"] ?? "";
        $fullname = $_POST["inpFullname"] ?? "";
        $password = $_POST["inpPassword"] ?? "";

        $status = Auth::UserAdd($username, $fullname, $password);

        switch ($status) {
            case Status::EXISTS:
                Url::Redirect("/Main/UsersGroups/userexists");
                break;
            case Status::SUCCESS:
                Url::Redirect("/Main/UsersGroups/usersuccess");
                break;
            default:
                Url::Redirect("/Main/UsersGroups/userfailed");
        }
    }

    public function DelGroup(): void {
        $groupname = $_POST["inpGroupname"] ?? "";

        $status = Auth::GroupDel($groupname);
        switch ($status) {
            case Status::NOTEXISTS:
                Url::Redirect("/Main/UsersGroups/groupnotexists");
                break;
            case Status::SUCCESS:
                Url::Redirect("/Main/UsersGroups/groupsuccess");
                break;
            default:
                Url::Redirect("/Main/UsersGroups/groupfailed");
        }
    }

    public function DelUser(): void {
        $username = $_POST["inpUsername"] ?? "";

        $status = Auth::UserDel($username);
        switch ($status) {
            case Status::NOTEXISTS:
                Url::Redirect("/Main/UsersGroups/usernotexists");
                break;
            case Status::SUCCESS:
                Url::Redirect("/Main/UsersGroups/usersuccess");
                break;
            default:
                Url::Redirect("/Main/UsersGroups/userfailed");
        }
    }

    public function Login(): void {
        $username = $_POST["inpUsername"] ?? "";
        $password = $_POST["inpPassword"] ?? "";

        if (Auth::Login($username, $password) == Status::SUCCESS) {
            Url::Redirect();
        } else {
            Url::Redirect("/Main/Login/fail");
        }
    }

    public function Membership(): void {
        $username = $_POST["inpUsername"] ?? "";
        $groupname = $_POST["inpGroupname"] ?? "";
        $action = $_POST["inpAction"] ?? "";

        switch ($action) {
            case "addUser":
                $status = Auth::UserJoin($username, $groupname);
                break;
            case "delUser":
                $status = Auth::UserLeave($username, $groupname);
                break;
            default:
                Url::Redirect();
        }

        switch ($status) {
            case Status::EXISTS:
                Url::Redirect("/Main/UsersGroups/membershipexists");
                break;
            case Status::SUCCESS:
                Url::Redirect("/Main/UsersGroups/membershipsuccess");
                break;
            default:
                Url::Redirect("/Main/UsersGroups/membershipfailed");
        }
    }
}
