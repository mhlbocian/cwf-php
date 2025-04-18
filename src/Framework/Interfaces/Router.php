<?php

namespace Framework\Interfaces;

use Framework\Query;

interface Router {

    public function __construct(?string $route);

    public function Execute(): void;

    public static function Get_Args(): array;

    public static function Get_Route(): string;
}
