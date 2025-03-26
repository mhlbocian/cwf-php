<?php

namespace Application\Models;

use Framework\Url;

class Sitemap {

    private static array $sitemap = [
        "Main" => [
            "Index" => "Main page",
            "About" => "About project"
        ],
        "Database" => [
            "Index" => "Database tests"
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
                $output[] = [$url, $description];
            }
        }

        return $output;
    }

    public static function Title(string $curr_page): string {
        $cont_act = explode("/", $curr_page);

        return self::$sitemap[$cont_act[0]][$cont_act[1]] ?? "";
    }
}
