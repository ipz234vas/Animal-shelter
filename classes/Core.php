<?php

namespace classes;

use classes\database\DB;
use ReflectionMethod;

class Core
{
    public string $defaultLayoutPath = 'layout/index.php';
    private static ?self $instance = null;
    protected Template $mainTemplate;
    public string $module;
    public string $action;
    public DB $db;

    private function __construct()
    {
        $this->mainTemplate = new Template($this->defaultLayoutPath);
        $host = Config::getInstance()->dbHost;
        $name = Config::getInstance()->dbName;
        $login = Config::getInstance()->dbLogin;
        $password = Config::getInstance()->dbPassword;
        $this->db = new DB($host, $name, $login, $password);
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
        if (isset($_GET['route'])) {
            $route = $_GET['route'];
            $route_parts = explode('/', $route);
            $this->module = $route_parts[0];
            if (isset($route_parts[1]))
                $this->action = $route_parts[1];
            else {
                $this->action = 'index';
            }
        } else {
            $this->module = 'home';
            $this->action = 'index';
        }
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

    public function done(): void
    {
        $this->mainTemplate->display();
    }

    public function error(int $code): void
    {
        http_response_code($code);
        die;
    }
}