<?php

namespace classes\exceptions;

use Exception;

class HttpException extends Exception
{
    public function __construct(int $code = 500, string $message = '')
    {
        parent::__construct($message ?: self::defaultMessage($code), $code);
    }

    private static function defaultMessage(int $code): string
    {
        return match ($code) {
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            422 => 'Unprocessable Entity',
            500 => 'Internal Server Error',
            default => 'HTTP Error',
        };
    }
}
