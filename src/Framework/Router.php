<?php

/*
 * CWF-PHP Framework
 * 
 * File: Framework\Router.php
 * Description: Router class
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework;

use Framework\Exceptions\Router_Exception;

final class Router implements Interfaces\Router {
    
    private string $action;
    private string $controller;
    private string $class_fqn;
    private string $default_action;
    private string $default_controller;
    private string $namespace;
    private static array $args = [];
    private static string $route = "";
    
    #[\Override]
    public function __construct(?string $route) {
        $config = Config::File("application")->Get("router");
        $this->namespace = $config["namespace"];
        $this->default_controller = $config["default_controller"];
        $this->default_action = $config["default_action"];

        $this->Parse_Route($route);
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
    
    #[\Override]
    public static function Get_Route(): string {

        return self::$route;
    }
    
    private function Check_Route(): void {
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
    
    private function Parse_Route(?string $route): void {
        if ($route == null || $route == "/") {
            $this->controller = $this->default_controller;
            $this->action = $this->default_action;
        } else {
            $path_array = \explode("/", $route);

            /*
             * If the route begins with `/` the first element of array is an
             * empty string. Remove it
             */
            if ($path_array[0] == "") {
                $path_array = \array_slice($path_array, 1);
            }

            $this->controller = $path_array[0];

            /*
             * If the action is not specified or path string ends with `/`
             */
            if (!isset($path_array[1]) || $path_array[1] == "") {
                $this->action = $this->default_action;
            } else {
                $this->action = $path_array[1];
            }

            if (\count($path_array) > 2 && $path_array[2] != "") {
                self::$args = \array_slice($path_array, 2);
            }
        }

        $this->class_fqn = $this->namespace . "\\" . $this->controller;
        self::$route = "/{$this->controller}/{$this->action}";
    }
}
