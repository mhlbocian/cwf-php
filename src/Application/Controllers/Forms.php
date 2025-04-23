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

        $status = Auth::Instance()->GroupAdd($groupname, $description);

        switch ($status) {
            case Status::EXISTS:
                Url::Redirect("/Main/Auth/groupexists");
                break;
            case Status::SUCCESS:
                Url::Redirect("/Main/Auth/groupsuccess");
                break;
            default:
                Url::Redirect("/Main/Auth/groupfailed");
        }
    }

    public function AddUser(): void {
        $username = $_POST["inpUsername"] ?? "";
        $fullname = $_POST["inpFullname"] ?? "";
        $password = $_POST["inpPassword"] ?? "";

        $status = Auth::Instance()->UserAdd($username, $fullname, $password);

        switch ($status) {
            case Status::EXISTS:
                Url::Redirect("/Main/Auth/userexists");
                break;
            case Status::SUCCESS:
                Url::Redirect("/Main/Auth/usersuccess");
                break;
            default:
                Url::Redirect("/Main/Auth/userfailed");
        }
    }

    public function DelGroup(): void {
        $groupname = $_POST["inpGroupname"] ?? "";

        $status = Auth::Instance()->GroupDel($groupname);
        switch ($status) {
            case Status::NOTEXISTS:
                Url::Redirect("/Main/Auth/groupnotexists");
                break;
            case Status::SUCCESS:
                Url::Redirect("/Main/Auth/groupsuccess");
                break;
            default:
                Url::Redirect("/Main/Auth/groupfailed");
        }
    }

    public function DelUser(): void {
        $username = $_POST["inpUsername"] ?? "";

        $status = Auth::Instance()->UserDel($username);
        switch ($status) {
            case Status::NOTEXISTS:
                Url::Redirect("/Main/Auth/usernotexists");
                break;
            case Status::SUCCESS:
                Url::Redirect("/Main/Auth/usersuccess");
                break;
            default:
                Url::Redirect("/Main/Auth/userfailed");
        }
    }

    public function Login(): void {
        $username = $_POST["inpUsername"] ?? "";
        $password = $_POST["inpPassword"] ?? "";

        if (Auth::Instance()->Login($username, $password) == Status::SUCCESS) {
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
                $status = Auth::Instance()->UserJoin($username, $groupname);
                break;
            case "delUser":
                $status = Auth::Instance()->UserLeave($username, $groupname);
                break;
            default:
                Url::Redirect();
        }

        switch ($status) {
            case Status::EXISTS:
                Url::Redirect("/Main/Auth/membershipexists");
                break;
            case Status::SUCCESS:
                Url::Redirect("/Main/Auth/membershipsuccess");
                break;
            default:
                Url::Redirect("/Main/Auth/membershipfailed");
        }
    }
}
