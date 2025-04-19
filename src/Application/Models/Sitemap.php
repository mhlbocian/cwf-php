<?php

namespace Application\Models;

use Framework\Url;

class Sitemap {

    private static array $main_menu = [
        "Main" => [
            "Index" => "Main page",
            "License" => "License",
            "Usage" => "Framework usage",
            "API" => "Framework API"
        ],
    ];
    private static array $api_menu = [
        "Database" => [
            "Configuration" => "Db_Config",
            "Connections" => "Db_Conn",
            "Queries" => "Db_Queries"
        ],
        "Authentication" => [
            "Configuration" => "Auth_Config",
            "Database driver" => "Auth_Database",
            "JSON driver" => "Auth_Json",
            "Tests site" => "/Authenticate"
        ]
    ];

    public static function ApiMenu(?string $curr_page): array {
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

    public static function ApiSiteExists(string $site): bool {

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
