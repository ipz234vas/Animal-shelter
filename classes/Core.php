<?php

namespace classes;

use ReflectionMethod;

class Core
{
    private static $instance = null;
    protected Template $mainTemplate;
    public string $module;
    public string $action;

    private function __construct()
    {
        $this->mainTemplate = new Template('layout/index.php');
    }

    public static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function init(): void
    {
        session_start();
    }

    public function run(): void
    {
        $route = $_GET['route'];
        $route_parts = explode('/', $route);
        $this->module = $route_parts[0];
        $this->action = $route_parts[1];
        $class_name = "controllers\\" . ucfirst($this->module) . 'Controller';
        if (!class_exists($class_name)) {
            $this->error(404);
        }
        $controller = new $class_name();
        $method = $this->action . "Action";
        if (!method_exists($controller, $method)) {
            $this->error(404);
        }

        $ref = new ReflectionMethod($controller, $method);
        $args = [];

        foreach ($ref->getParameters() as $param) {
            $name = $param->getName();

            if (array_key_exists($name, $_GET)) {
                $args[] = $_GET[$name];
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } elseif ($param->allowsNull()) {
                $args[] = null;
            } else {
                $this->error(400, "Missing required parameter: $name");
            }
        }

        $data = $controller->$method(...$args);
        $this->mainTemplate->addParams($data);
    }

    public function done()
    {
        $this->mainTemplate->display();
    }

    public function error(int $code): void
    {
        http_response_code($code);
        die;
    }
}