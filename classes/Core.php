<?php

namespace classes;

class Core
{
    private static $instance = null;
    protected Template $mainTemplate;

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
        $module = $route_parts[0];
        $action = $route_parts[1];
        $class_name = "controllers\\" . ucfirst($module) . 'Controller';
        if (!class_exists($class_name)) {
            $this->error(404);
        }
        $controller = new $class_name();
        $method = $action . "Action";
        if (!method_exists($controller, $method)) {
            $this->error(404);
        }
        $data = $controller->$method();
        $this->mainTemplate->addParams($data);
    }

    public function done()
    {
        $this->mainTemplate->display();
    }

    public function error(int $code): void
    {
        http_response_code(404);
        die;
    }
}