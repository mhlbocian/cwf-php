<?php

namespace Application\Models;

use Framework\Url;

class Sitemap {

    private static array $mainmenu = [
        "Main" => [
            "Index" => "Main page",
            "License" => "License",
            "Usage" => "Framework usage",
            "API" => "Framework API"
        ],
    ];
    private static array $apimenu = [
        "Database" => [
            "Configuration" => "Db_Config",
            "Connections" => "Db_Conn",
            "Queries" => "Db_Queries"
        ],
        "Authentication" => [
            "Configuration" => "Auth_Config",
            "Tests site" => "/Authenticate"
        ]
    ];

    public static function ApiMenu(?string $curr_page): array {
        $output = [];

        foreach (self::$apimenu as $section => $subpages) {

            foreach ($subpages as $subpage => $link) {
                
                if($curr_page == $link){
                    $output[$section][$subpage] = null;
                }else{
                    $output[$section][$subpage] = $link;
                }
                
            }
            
        }
        
        return $output;
    }

    public static function ApiSiteExists(string $site): bool {

        foreach (self::$apimenu as $section => $subpages) {

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

        foreach (self::$mainmenu as $controller => $actions) {

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

        return self::$mainmenu[$ca_arr[0]][$ca_arr[1]] ?? "";
    }
}
