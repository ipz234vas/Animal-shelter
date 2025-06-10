<?php

namespace classes;

use classes\database\DB;
use classes\exceptions\HttpException;
use ErrorException;
use ReflectionException;
use ReflectionMethod;

class Core
{
    public string $defaultLayoutPath = 'layout/index.php';
    private static ?self $instance = null;
    public Template $template;
    public string $module;
    public string $action;
    public DB $db;

    private function __construct()
    {
        $this->template = new Template($this->defaultLayoutPath);
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

        set_exception_handler([$this, 'handleException']);
        set_error_handler([$this, 'handleError']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    /**
     * @throws ReflectionException
     * @throws HttpException
     */
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
        $this->template->addParams($data);
    }

    public function done(): void
    {
        $this->template->display();
    }

    /**
     * @throws HttpException
     */
    public function error(int $code, ?string $message = ""): void
    {
        throw new HttpException($code, $message);
    }

    public function handleException(\Throwable $e): void
    {
        if ($e instanceof HttpException) {
            $this->handleHttpException($e);
        } else {
            $this->handleHttpException(new HttpException(500, 'Internal Server Error'));
        }
    }

    /**
     * @throws ErrorException
     */
    public function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    public function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $this->handleHttpException(new HttpException(500, 'Fatal error'));
        }
    }

    public function handleHttpException(HttpException $e): void
    {
        ob_clean();

        $code = $e->getCode();
        http_response_code($code);

        if ($code === 401 && !$this->isApiRequest()) {
            $redirectTo = '/?route=auth/login';
            $currentUrl = $_SERVER['REQUEST_URI'] ?? '/';
            header("Location: $redirectTo&next=" . urlencode($currentUrl));
            exit;
        }

        if ($this->isApiRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'error' => $e->getMessage(),
                'code' => $code
            ]);
            exit;
        }

        $view = __DIR__ . "/../views/errors/{$code}.php";
        if (file_exists($view)) {
            include $view;
        } else {
            echo "<h1>$code</h1><p>{$e->getMessage()}</p>";
        }

        exit;
    }

    private function isApiRequest(): bool
    {
        return isset($_SERVER['HTTP_X_API_REQUEST']) ||
            str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
    }
}