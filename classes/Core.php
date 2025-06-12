<?php

namespace classes;

use attributes\auth\Authorize;
use attributes\routing\Route;
use classes\database\DB;
use classes\exceptions\HttpException;
use JsonException;
use models\User;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class Core
{
    private static ?self $instance = null;

    private ArgumentResolver $argumentResolver;
    private ErrorHandler $errors;
    private Router $router;
    public Template $template;
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

        $this->errors = new ErrorHandler();
        //$this->useErrorHandler();

        $cng = Config::getInstance();
        $this->errors->setLoginPath($cng->loginPath);
        $this->template = new Template($cng->layoutPath);
        $this->db = new DB($cng->dbHost, $cng->dbName, $cng->dbLogin, $cng->dbPassword);
        $this->session = new Session();
        $this->argumentResolver = new ArgumentResolver(new ModelBinder());
        $this->router = new Router($cng->defaultModule, $cng->defaultAction);
    }

    /**
     * @throws ReflectionException
     * @throws HttpException
     * @throws JsonException
     */
    public function run(): void
    {
        [$this->module, $this->action] = $this->router->parse($_GET['route'] ?? '');
        $httpMethod = $_SERVER['REQUEST_METHOD'];

        $isApi = $this->isApiRequest();

        [$this->module, $this->action] = $this->router->parse($_GET['route'] ?? '');

        if ($isApi) {
            $controllerClass = "controllers\\api\\" . ucfirst($this->module) . 'ApiController';
        } else {
            $controllerClass = "controllers\\" . ucfirst($this->module) . 'Controller';
        }

        if (!class_exists($controllerClass)) {
            throw new HttpException(404);
        }

        $controller = new $controllerClass();
        $reflection = new ReflectionClass($controller);

        $matchedMethod = null;

        foreach ($reflection->getMethods() as $method) {
            foreach ($method->getAttributes() as $attr) {
                $instance = $attr->newInstance();
                if ($instance instanceof Route) {
                    if ($instance->method === $httpMethod && $instance->action === $this->action) {
                        $matchedMethod = $method;
                        break 2;
                    }
                }
            }
        }

        if (!$matchedMethod) {
            throw new HttpException(404, "No matching route for action '{$this->action}' and method '$httpMethod'");
        }

        $this->handleAuthorization($reflection, $matchedMethod);

        $params = $_REQUEST;
        if ($isApi && $_SERVER['REQUEST_METHOD'] !== 'GET') {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (str_starts_with($contentType, 'application/json')) {
                $raw = file_get_contents('php://input');
                $json = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                    $params = array_merge($params, $json);
                }
            }
        }

        $modelState = $controller->modelState;
        $args = $this->argumentResolver->resolve($matchedMethod, $params, $modelState);
        $data = $matchedMethod->invoke($controller, ...$args);

        if ($isApi) {
            if (!headers_sent()) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            }
            return;
        }

        $this->template->addParams($data);
    }

    /**
     * @throws HttpException
     */
    private function handleAuthorization(ReflectionClass $controller, ReflectionMethod $method): void
    {
        /** @var Authorize[] $authAttributes */
        $authAttributes = [];

        foreach ($controller->getAttributes(Authorize::class) as $attr) {
            $authAttributes[] = $attr->newInstance();
        }
        foreach ($method->getAttributes(Authorize::class) as $attr) {
            $authAttributes[] = $attr->newInstance();
        }

        if (empty($authAttributes)) {
            return;
        }

        $userId = $this->session->get('user_id');
        if (!$userId) {
            throw new HttpException(401);
        }

        $userPermissionsStr = User::getPermissionsById($userId) ?? "";
        $userPermissions = PermissionParser::fromString($userPermissionsStr);

        foreach ($authAttributes as $authorize) {
            if (!$authorize->isLoginOnly()) {
                foreach ($authorize->permissions as $permission) {
                    if (!in_array($permission, $userPermissions, true)) {
                        throw new HttpException(403);
                    }
                }
            }
        }
    }

    public function done(): void
    {
        $this->template->display();
    }

    private function useErrorHandler(): void
    {
        set_exception_handler($this->errors->handle(...));
        set_error_handler($this->errors->convertErrorToException(...));
        register_shutdown_function($this->errors->handleShutdown(...));
    }

    private function isApiRequest(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        return str_contains($accept, 'application/json')
            || str_contains($contentType, 'application/json');
    }

}