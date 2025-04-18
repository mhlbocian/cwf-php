<?php

namespace Framework\Interfaces;

interface View {

    function __construct(string $view);

    public function Bind(string $var, mixed $val): void;

    public function __toString(): string;
}
