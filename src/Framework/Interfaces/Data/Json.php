<?php

namespace Framework\Interfaces\Data;

interface Json {

    public function __construct(string $file);

    public function Fetch(): array;

    public function Get(string $key): mixed;

    public function Set(string $key, mixed $value): void;

    public function Unset(string $key): void;
}
