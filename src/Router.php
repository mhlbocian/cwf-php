<?php

/*
 * CWF-PHP Framework
 * 
 * File: Router.php
 * Description: Router class
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp;

use CwfPhp\CwfPhp\Exceptions\Router_Exception;

final class Router implements Interfaces\Router {
    /* config values */

    private readonly string $default_action;
    private readonly string $default_controller;
    private readonly string $namespace;
    private readonly bool $pointers_only;
    private ?array $pointers;
    /* current route values */
    private readonly string $action;
    private readonly string $controller;
    private readonly string $class_fqn;
    /* available outside via Get_Args() */
    private static array $args = [];

    #[\Override]
    public function __construct(?string $route) {
        if (!Config::Json("router")->Exists()) {

            throw new \Error("ROUTER: no configuration file 'router.json'");
        }

        $config = Config::Json("router")->Fetch();
        $this->Parse_Router_Config($config);
        $this->Parse_Route($route ?? "/");
    }

    #[\Override]
    public function Execute(): void {
        $this->Check_Route();
        $ctrl_object = new $this->class_fqn();
        $ctrl_object->{$this->action}();
    }

    #[\Override]
    public static function Get_Args(): array {

        return self::$args;
    }

    private function Check_Route(): void {
        $this->class_fqn = "{$this->namespace}\\{$this->controller}";

        if (!\class_exists($this->class_fqn)) {

            throw new Router_Exception("ROUTER: class '{$this->class_fqn}' does not exist");
        }

        if (!\method_exists($this->class_fqn, $this->action)) {

            throw new Router_Exception("ROUTER: method '{$this->action}' does not exist");
        }

        if (\str_starts_with($this->action, "__")) {

            throw new Router_Exception("ROUTER: action forbidden for magic methods");
        }
    }

    private function Parse_Router_Config(array $config): void {
        if (!key_exists("namespace", $config)) {

            throw new \Error("ROUTER: no 'namespace' key in the 'router.json'");
        }

        $this->namespace = $config["namespace"];
        $this->default_controller = $config["default_controller"] ?? "Main";
        $this->default_action = $config["default_action"] ?? "Index";
        $this->pointers_only = $config["pointers_only"] ?? false;

        if (\key_exists("pointers", $config)) {
            $this->Parse_Pointers($config["pointers"]);
        } else {
            $this->pointers = null;
        }
    }

    private function Parse_Pointers(array $pointers): void {
        foreach ($pointers as $name => $args) {
            if (!\preg_match("#^[\p{L}\p{N}_-]+$#u", $name)) {

                throw new \Error("ROUTER: Invalid pointer name '{$name}'");
            }

            if (!\key_exists("controller", $args)) {

                throw new \Error("ROUTER: No controller specified in the pointer '{$name}'");
            }

            $this->pointers[$name] = [
                "controller" => $args["controller"],
                "action" => $args["action"] ?? $this->default_action
            ];
        }
    }

    private function Parse_Route(string $route): void {
        if ($route == "/") {
            $this->controller = $this->default_controller;
            $this->action = $this->default_action;

            return;
        }

        $preParts = $this->Route_Preprocess($route);
        // pointers name are already preprocessed
        if (\key_exists($preParts[0], $this->pointers)) {
            $pointer = $this->pointers[$preParts[0]];
            $this->controller = $pointer["controller"];
            $this->action = $pointer["action"] ?? $this->default_action;
            self::$args = \array_slice($preParts, 2);

            return;
        }

        if ($this->pointers_only) {

            throw new Router_Exception("ROUTER: invalid route");
        }

        $postParts = $this->Route_Postprocess($route);
        $this->controller = $postParts["controller"];
        $this->action = $postParts["action"] ?? $this->default_action;
        self::$args = $postParts["args"];
    }

    private function Route_Postprocess(string $route): array {
        $pattern = '#^(?:/|/(?:[A-Za-z][A-Za-z0-9_]*|[A-Za-z][A-Za-z0-9_]*'
                . '/[A-Za-z][A-Za-z0-9_]*(?:/[\%\p{L}\p{N}_-]+)*))/*$#u';

        if (!\preg_match($pattern, $route, $parts)) {

            throw new Router_Exception("ROUTER: invalid route format");
        }

        $parts = \explode("/", \trim($route, "/"));

        return [
            "controller" => $parts[0] ?? null,
            "action" => $parts[1] ?? null,
            "args" => \array_slice($parts, 2)
        ];
    }

    private function Route_Preprocess(string $route): array {
        $pattern = "#^(?:/[\%\p{L}\p{N}_-]+)*/*$#u";

        if (!\preg_match($pattern, $route)) {

            throw new Router_Exception("ROUTER: invalid route format");
        }

        return \explode("/", trim($route, "/"));
    }
}
