<?php

namespace Application\Models;

use Framework\Url;

class Sitemap {

    private static array $sitemap = [
        "Main" => [
            "Index" => "Main page",
            "About" => "About project",
            "Examples" => "Code Examples"
        ],
        "Database" => [
            "Index" => "Database support"
        ]
    ];

    public static function Menu(string $curr_page): array {
        $output = [];
        foreach (self::$sitemap as $controller => $actions) {
            foreach ($actions as $action => $description) {
                $site = "{$controller}/{$action}";
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

        return self::$sitemap[$ca_arr[0]][$ca_arr[1]] ?? "";
    }
}
