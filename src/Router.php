<?php

/*
 * CWF-PHP Framework
 * 
 * File: Router.php
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp;

use CwfPhp\CwfPhp\Exceptions\InvalidRouteException;
use CwfPhp\CwfPhp\Interfaces\RouterInterface;

/**
 * Router class
 */
final class Router implements RouterInterface {

    /**
     * 
     * @var string Default action, when is not specified in the URL
     */
    private readonly string $defaultAction;

    /**
     * 
     * @var string Default controller, when is not specified in the URL
     */
    private readonly string $defaultController;

    /**
     * 
     * @var string Namespace, where controllers are stored
     */
    private readonly string $namespace;

    /**
     * 
     * @var bool If true, only pointers (static routes) are accepted
     */
    private readonly bool $onlyPointers;

    /**
     * 
     * @var array An array of pointers (static routes)
     */
    private array $pointers = [];

    /**
     * 
     * @var string Current action
     */
    private static string $action;

    /**
     * 
     * @var string Current controller
     */
    private static string $controller;

    /**
     * 
     * @var string|null Current pointer (if specified)
     */
    private static ?string $pointer = null;

    /**
     * 
     * @var bool Check, if the routing process is done
     */
    private static bool $routed = false;

    /**
     * 
     * @var string Controller class fully qualified name (with namespace)
     */
    private readonly string $classFqn;

    /**
     * 
     * @var array Arguments that can be accessed via getArgs method
     */
    private static array $args = [];

    /**
     * Read router configuration and process the route
     * 
     * @param string|null $route Empty or like "/aaa[/bbb[/ccc[/..."
     * @throws \Error
     */
    #[\Override]
    public function __construct(?string $route) {
        if (!Config::file("router.json")->exists()) {

            throw new \Error("[router.json] file not exists");
        }

        $config = Config::file("router.json")->fetch();
        $this->parseConfig($config);
        $this->parseRoute($route ?? "/");
        self::$routed = true;
    }

    /**
     * Executes the route. If the route is invalid, executes $onInvalidRoute.
     * If an execution error occurs, executes $onError or, if is not specified
     * $onInvalidRoute is executed.
     * 
     * @param callable|null $onInvalidRoute Specify action, when route is wrong
     * @param callable|null $onError Specify action for execution error
     * @return void
     */
    #[\Override]
    public function execute(
            callable $onInvalidRoute,
            ?callable $onError = null): void {

        try {
            $this->checkRoute();
            $ctrl_object = new $this->classFqn();
            $ctrl_object->{self::$action}();
        } catch (InvalidRouteException) {
            $onInvalidRoute();
        } catch (\Throwable) {
            if (\is_null($onError)) {
                $onInvalidRoute();
            } else {
                $onError();
            }
        }
    }

    /**
     * Get argumets for the action
     * 
     * @param bool $withEnv When specified, includes the controller and action
     * @return array
     */
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

    /**
     * Check, if the controller and/or method exists
     * 
     * @return void
     * @throws InvalidRouteException
     */
    private function checkRoute(): void {
        $this->classFqn = "{$this->namespace}\\" . self::$controller;

        if (!\class_exists($this->classFqn)) {

            throw new InvalidRouteException("Unknown controller '" . self::$controller . "'");
        }

        if (!\method_exists($this->classFqn, self::$action)) {

            throw new InvalidRouteException("Unknown action '" . self::$action . "'");
        }

        if (\str_starts_with(self::$action, "__")) {

            throw new InvalidRouteException("Action forbidden for magic methods");
        }
    }

    /**
     * Parses the router configuration
     * 
     * @param array $config Parsed JSON array from the router.json file
     * @return void
     * @throws \Error
     */
    private function parseConfig(array $config): void {
        if (!key_exists("namespace", $config)) {

            throw new \Error("[router.json] no namespace key");
        }

        $this->namespace = $config["namespace"];
        $this->defaultController = $config["defaultController"] ?? "Main";
        $this->defaultAction = $config["defaultAction"] ?? "Index";
        $this->onlyPointers = $config["pointersOnly"] ?? false;

        if (\key_exists("pointers", $config)) {
            $this->parsePointers($config["pointers"]);
        }
    }

    /**
     * Parses the pointers array and check the valid name and the existance
     * of the controller and/or action
     * 
     * @param array $pointers Pointers array from the configuration
     * @return void
     * @throws \Error
     */
    private function parsePointers(array $pointers): void {
        foreach ($pointers as $name => $args) {
            if (!\preg_match("#^[\p{L}\p{N}_-]+$#u", $name)) {

                throw new \Error("[router.json] wrong pointer name '{$name}'");
            }

            if (!\key_exists("controller", $args)) {

                throw new \Error("[router.json] no controller for pointer '{$name}'");
            }

            $this->pointers[$name] = [
                "controller" => $args["controller"],
                "action" => $args["action"] ?? $this->defaultAction
            ];
        }
    }

    /**
     * Parses the route and sets the right properties in the class
     * 
     * @param string $route
     * @return void
     * @throws InvalidRouteException
     */
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

            throw new InvalidRouteException("Unknown route");
        }

        $postParts = $this->postprocessRoute($route);
        $this->setRoute($postParts["controller"],
                $postParts["action"],
                $postParts["args"]);
    }

    /**
     * Postprocessing for non-pointers routes
     * 
     * @param string $route
     * @return array
     * @throws InvalidRouteException
     */
    private function postprocessRoute(string $route): array {
        $pattern = '#^(?:/|/(?:[A-Za-z][A-Za-z0-9_]*|[A-Za-z][A-Za-z0-9_]*'
                . '/[A-Za-z][A-Za-z0-9_]*(?:/[\%\p{L}\p{N}_-]+)*))/*$#u';

        if (!\preg_match($pattern, $route)) {

            throw new InvalidRouteException("Invalid route format");
        }

        $parts = \explode("/", \trim($route, "/"));

        return [
            "controller" => $parts[0] ?? null,
            "action" => $parts[1] ?? null,
            "args" => \array_slice($parts, 2)
        ];
    }

    /**
     * Preproceses the route. Check the right format and move on
     * 
     * @param string $route
     * @return array
     * @throws InvalidRouteException
     */
    private function preprocessRoute(string $route): array {
        $pattern = "#^(?:/[\%\p{L}\p{N}_-]+)*/*$#u";

        if (!\preg_match($pattern, $route)) {

            throw new InvalidRouteException("Invalid route format");
        }

        return \explode("/", trim($route, "/"));
    }

    /**
     * Sets the class properties
     * 
     * @param string|null $controller
     * @param string|null $action
     * @param array $args
     * @param string|null $pointer
     * @return void
     */
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
