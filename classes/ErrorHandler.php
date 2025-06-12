<?php

namespace classes;

use classes\exceptions\HttpException;
use Throwable;
use ErrorException;

final class ErrorHandler
{
    private string $loginPath = '';

    public function setLoginPath(string $path): void
    {
        $this->loginPath = $path;
    }

    public function handle(Throwable $e): void
    {
        if (!$e instanceof HttpException) {
            $e = new HttpException(500, 'Internal Server Error');
        }
        $this->renderHttpException($e);
    }

    /**
     * @throws ErrorException
     */
    public function convertErrorToException(int $errno, string $msg, string $file, int $line): bool
    {
        throw new ErrorException($msg, 0, $errno, $file, $line);
    }

    public function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            $this->renderHttpException(new HttpException(500, 'Fatal error'));
        }
    }

    private function renderHttpException(HttpException $e): void
    {
        ob_clean();
        $code = $e->getCode() ?: 500;
        http_response_code($code);

        $isApi = isset($_SERVER['HTTP_X_API_REQUEST'])
            || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
            || str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json');

        if ($code === 401 && !empty($this->loginPath) && !$isApi) {
            $uri = base64_encode($_SERVER['REQUEST_URI'] ?? '/');
            $login = $this->loginPath . '?next=' . $uri;

            header("Location: $login");
            exit;
        }

        if ($isApi) {
            header('Content-Type: application/json');
            echo json_encode(['errors' => $e->getMessage(), 'code' => $code]);
        } else {
            $view = __DIR__ . "/../../views/errors/{$code}.php";
            echo file_exists($view)
                ? file_get_contents($view)
                : "<h1>{$code}</h1><p>{$e->getMessage()}</p>";
        }
        exit;
    }
}