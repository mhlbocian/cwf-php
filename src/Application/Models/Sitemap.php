<?php

namespace Application\Models;

use Framework\Url;

class Sitemap {

    private static array $main_menu = [
        "Main" => [
            "Index" => "Main page",
            "License" => "License",
            "Usage" => "Usage",
            "Manual" => "Manual",
            "Auth" => "Authentication",
            "Login" => "Sign in"
        ],
    ];
    private static array $api_menu = [
        "Authentication" => [
            "Configuration" => "Auth_Configuration",
            "Database driver" => "Auth_DriverDb",
            "JSON driver" => "Auth_DriverJson",
            "Custom driver" => "Auth_CustomDrivers"
        ],
        "Configuration" => [
            "Creating files" => "Config_CreateFile",
            "Fetching data" => "Config_FetchData",
            "Updating data" => "Config_UpdateData"
        ],
        "Data managment" => [
            "JSON files" => "Data_Json"
        ],
        "Database" => [
            "Configuration" => "Database_Config",
            "Connections" => "Database_Connections",
            "Queries" => "Database_Queries"
        ],
        "Framework" => [
            "Routing" => "Framework_Routing",
            "URL" => "Framework_Url",
            "Views" => "Framework_Views"
        ]
    ];

    public static function ManualMenu(?string $curr_page): array {
        $output = [];

        foreach (self::$api_menu as $section => $subpages) {
            foreach ($subpages as $subpage => $link) {
                if ($curr_page == $link) {
                    $output[$section][$subpage] = null;
                } else {
                    $output[$section][$subpage] = $link;
                }
            }
        }

        return $output;
    }

    public static function ManualExists(string $site): bool {
        foreach (self::$api_menu as $section => $subpages) {
            foreach ($subpages as $subpage => $link) {
                if ($link == $site) {

                    return true;
                }
            }
        }

        return false;
    }

    public static function MainMenu(string $curr_page): array {
        $output = [];

        foreach (self::$main_menu as $controller => $actions) {
            foreach ($actions as $action => $description) {
                $site = "/{$controller}/{$action}";

                if ($site == $curr_page) {
                    $url = null;
                } else {
                    $url = Url::Site($site);
                }

                $output[] = ["url" => $url, "description" => $description];
            }
        }

        return $output;
    }

    public static function Title(string $current_page): string {
        $ca_arr = explode("/", $current_page);

        return self::$main_menu[$ca_arr[0]][$ca_arr[1]] ?? "";
    }
}
