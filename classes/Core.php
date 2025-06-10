<?php

namespace classes;

use classes\database\DB;
use classes\exceptions\HttpException;
use ReflectionException;
use ReflectionMethod;

class Core
{
    private static ?self $instance = null;

    private Template $template;
    private ArgumentResolver $argumentResolver;
    private ErrorHandler $errors;
    private Router $router;
    public DB $db;
    public Session $session;
    public string $module;
    public string $action;

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function init(): void
    {
        session_start();

        $this->useErrorHandler();

        $cng = Config::getInstance();
        $this->errors->setLoginPath($cng->loginPath);
        $this->template = new Template($cng->layoutPath);
        $this->db = new DB($cng->dbHost, $cng->dbName, $cng->dbLogin, $cng->dbPassword);
        $this->session = new Session();
        $this->argumentResolver = new ArgumentResolver();
        $this->router = new Router($cng->defaultModule, $cng->defaultAction);
    }

    /**
     * @throws ReflectionException
     * @throws HttpException
     */
    public function run(): void
    {
        [$this->module, $this->action] = $this->router->parse($_GET['route'] ?? '');

        $controllerClass = "controllers\\" . ucfirst($this->module) . 'Controller';
        if (!class_exists($controllerClass)) {
            throw new HttpException(404);
        }

        $controller = new $controllerClass();
        $method = $this->action . "Action";

        if (!method_exists($controller, $method)) {
            throw new HttpException(404);
        }

        $ref = new ReflectionMethod($controller, $method);
        $args = $this->argumentResolver->resolve($ref, $_GET);
        $data = $controller->$method(...$args);

        $this->template->addParams($data);
    }

    public function done(): void
    {
        $this->template->display();
    }

    private function useErrorHandler(): void
    {
        $this->errors = new ErrorHandler();
        set_exception_handler($this->errors->handle(...));
        set_error_handler($this->errors->convertErrorToException(...));
        register_shutdown_function($this->errors->handleShutdown(...));
    }
}