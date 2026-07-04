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

use CwfPhp\CwfPhp\Exceptions\RouterException;
use CwfPhp\CwfPhp\Interfaces\RouterInterface;

final class Router implements RouterInterface {

    /** config values */
    private readonly string $defaultAction;
    private readonly string $defaultController;
    private readonly string $namespace;
    private readonly bool $onlyPointers;
    private array $pointers = [];

    /** current route values */
    private static string $action;
    private static string $controller;
    private static ?string $pointer = null;
    private static bool $routed = false;
    private readonly string $classFqn;

    /** available outside via getArgs() */
    private static array $args = [];

    #[\Override]
    public function __construct(?string $route) {
        if (!Config::file("router.json")->exists()) {

            throw new \Error("ROUTER: no configuration file 'router.json'");
        }

        $config = Config::file("router.json")->fetch();
        $this->parseConfig($config);
        $this->parseRoute($route ?? "/");
        $this->checkRoute();
        self::$routed = true;
    }

    #[\Override]
    public function execute(): void {
        $ctrl_object = new $this->classFqn();
        $ctrl_object->{self::$action}();
    }

    #[\Override]
    public static function getArgs(bool $withEnv = false): array {
        if (!self::$routed) {

            return [];
        }

        if ($withEnv) {

            return array_merge([
                self::$pointer ?? self::$controller,
                self::$action], self::$args);
        }

        return self::$args;
    }

    private function checkRoute(): void {
        $this->classFqn = "{$this->namespace}\\" . self::$controller;

        if (!\class_exists($this->classFqn)) {

            throw new RouterException("ROUTER: class '{$this->classFqn}'"
                            . " does not exist");
        }

        if (!\method_exists($this->classFqn, self::$action)) {

            throw new RouterException("ROUTER: method '" . self::$action
                            . "' does not exist");
        }

        if (\str_starts_with(self::$action, "__")) {

            throw new RouterException("ROUTER: action forbidden for magic"
                            . "methods");
        }
    }

    private function parseConfig(array $config): void {
        if (!key_exists("namespace", $config)) {

            throw new \Error("ROUTER: no 'namespace' key in the 'router.json'");
        }

        $this->namespace = $config["namespace"];
        $this->defaultController = $config["defaultController"] ?? "Main";
        $this->defaultAction = $config["defaultAction"] ?? "Index";
        $this->onlyPointers = $config["pointersOnly"] ?? false;

        if (\key_exists("pointers", $config)) {
            $this->parsePointers($config["pointers"]);
        }
    }

    private function parsePointers(array $pointers): void {
        foreach ($pointers as $name => $args) {
            if (!\preg_match("#^[\p{L}\p{N}_-]+$#u", $name)) {

                throw new \Error("ROUTER: Invalid pointer name '{$name}'");
            }

            if (!\key_exists("controller", $args)) {

                throw new \Error("ROUTER: No controller specified in the "
                                . "pointer '{$name}'");
            }

            $this->pointers[$name] = [
                "controller" => $args["controller"],
                "action" => $args["action"] ?? $this->defaultAction
            ];
        }
    }

    private function parseRoute(string $route): void {
        if ($route == "/") {
            $this->setRoute();

            return;
        }

        $preParts = $this->preprocessRoute($route);

        if (\key_exists($preParts[0], $this->pointers)) {
            $pointer = $this->pointers[$preParts[0]];
            $this->setRoute($pointer["controller"],
                    $pointer["action"],
                    \array_slice($preParts, 1),
                    $preParts[0]);

            return;
        }

        if ($this->onlyPointers) {

            throw new RouterException("ROUTER: invalid route");
        }

        $postParts = $this->postprocessRoute($route);
        $this->setRoute($postParts["controller"],
                $postParts["action"],
                $postParts["args"]);
    }

    private function postprocessRoute(string $route): array {
        $pattern = '#^(?:/|/(?:[A-Za-z][A-Za-z0-9_]*|[A-Za-z][A-Za-z0-9_]*'
                . '/[A-Za-z][A-Za-z0-9_]*(?:/[\%\p{L}\p{N}_-]+)*))/*$#u';

        if (!\preg_match($pattern, $route)) {

            throw new RouterException("ROUTER: invalid route format");
        }

        $parts = \explode("/", \trim($route, "/"));

        return [
            "controller" => $parts[0] ?? null,
            "action" => $parts[1] ?? null,
            "args" => \array_slice($parts, 2)
        ];
    }

    private function preprocessRoute(string $route): array {
        $pattern = "#^(?:/[\%\p{L}\p{N}_-]+)*/*$#u";

        if (!\preg_match($pattern, $route)) {

            throw new RouterException("ROUTER: invalid route format");
        }

        return \explode("/", trim($route, "/"));
    }

    private function setRoute(
            ?string $controller = null,
            ?string $action = null,
            array $args = [],
            ?string $pointer = null): void {

        self::$controller = $controller ?? $this->defaultController;
        self::$action = $action ?? $this->defaultAction;
        self::$args = $args;
        self::$pointer = $pointer;
    }
}
