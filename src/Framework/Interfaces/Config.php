<?php

namespace Framework\Interfaces;

interface Config {

    public static function Exists(string $cfg): bool;

    public static function Fetch(string $cfg): array;

    public static function Get(string $cfg, string $key): mixed;

    public static function Set(string $cfg, string $key, mixed $value): void;

    public static function Update(string $cfg): void;
}
