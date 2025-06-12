<?php

namespace classes;

abstract class ApiController extends BaseController
{
    protected function respond(mixed $payload, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload,
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function respondSuccess(mixed $data = null, array $meta = [], int $status = 200): never
    {
        $this->respond(['success' => true, 'data' => $data, 'meta' => $meta], $status);
    }

    protected function respondError(array|string $errors, int $status = 400, ?string $code = null): never
    {
        $body = ['success' => false];
        if (is_array($errors))
            $body['errors'] = $errors;
        else
            $body['message'] = $errors;
        if ($code)
            $body['code'] = $code;

        $this->respond($body, $status);
    }
}
