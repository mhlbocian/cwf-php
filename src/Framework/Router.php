<?php

/*
 * Custom Web Framework
 * 
 * Author: Michał Bocian <mhl.bocian@gmail.com>
 * License: 3-clause BSD
 */

namespace Framework;

/**
 * RouterException class for invalid route exception
 */
class RouterException extends \Exception {
    
}

/**
 * Router class
 */
class Router {

    private string $namespace;
    private string $default_controller;
    private string $default_action;
    private string $controller;
    private string $action;
    private static string $current;
    private array $data = [];

    public function __construct(?string $route) {
        $this->namespace = Config::Get("router")["namespace"];
        $this->default_controller = Config::Get("router")["default_controller"];
        $this->default_action = Config::Get("router")["default_action"];

        if ($route == null) {
            $this->controller = $this->default_controller;
            $this->action = $this->default_action;
        } else {
            $path_array = explode("/", $route);

            /*
             * If the route begins with `/` the first element of array is an
             * empty string. Remove it.
             */
            if ($path_array[0] == "") {
                $path_array = array_slice($path_array, 1);
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

            if (count($path_array) > 2 && $path_array[2] != "") {
                $this->data = array_slice($path_array, 2);
            }
        }
    }

    public function Execute(): void {
        $class_name = $this->namespace . "\\" . $this->controller;

        if (!class_exists($class_name)) {
            throw new RouterException("Class {$class_name} does not exist");
        }

        if (!method_exists($class_name, $this->action)) {
            throw new RouterException("Method {$this->action} does not exist");
        }

        self::$current = "{$this->controller}/{$this->action}";
        $controller = new $class_name();
        $controller->{$this->action}(...$this->data);
    }

    public static function Current(): string {
        return self::$current;
    }
}
