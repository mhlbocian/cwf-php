<?php

namespace Framework\Interfaces;

use Framework\Query;

interface Url {

    public static function Init(): void;

    public static function Site(string $path = "", bool $site = true): string;

    public static function Redirect(string $path = "", bool $site = true): void;
}
