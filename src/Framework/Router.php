<?php

/*
 * CWF-PHP Framework
 * 
 * File: Router.php
 * Description: Router class
 * Author: Michał Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework;

use Exception;

class Invalid_Route extends Exception {
    // InvalidRoute class for invalid route exception
}

class Router {

    private string $action;
    private string $controller;
    private string $class;
    private string $default_action;
    private string $default_controller;
    private string $namespace;
    private static array $params = [];
    private static string $route = "";

    /**
     * Parse route and prepares it for execution
     * 
     * @param string|null $route
     */
    public function __construct(?string $route) {
        $this->namespace = Config::Get("router")["namespace"];
        $this->default_controller = Config::Get("router")["default_controller"];
        $this->default_action = Config::Get("router")["default_action"];

        $this->Parse_Route($route);

        $this->class = $this->namespace . "\\" . $this->controller;
    }

    /**
     * Check all requirements before initializing controller
     * 
     * @return void
     */
    private function Check_Requirements(): void {
        if (!class_exists($this->class)) {
            throw new Invalid_Route("Class '{$this->class}' does not exist");
        }

        if (!method_exists($this->class, $this->action)) {
            throw new Invalid_Route("Method '{$this->action}' does not exist");
        }
    }

    /**
     * Parse route string and set properties
     * 
     * @param string|null $route
     * @return void
     */
    private function Parse_Route(?string $route): void {
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
                self::$params = array_slice($path_array, 2);
            }
        }

        self::$route = "{$this->controller}/{$this->action}";
    }

    /**
     * Check requirements and execute route
     * 
     * @return void
     * @throws Invalid_Route
     */
    public function Execute(): void {
        $this->Check_Requirements();

        $ctrl_object = new $this->class();
        $ctrl_object->{$this->action}();
    }

    /**
     * Return parameters array
     * URL: {Controller}/{Action}[/Par1[/[Par2]...]]
     * 
     * @return array
     */
    public static function Get_Params(): array {

        return self::$params;
    }

    /**
     * Return current route {controller}/{action} without parameters
     * 
     * @return string
     */
    public static function Get_Route(): string {

        return self::$route;
    }
}
