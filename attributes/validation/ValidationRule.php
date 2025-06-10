<?php

namespace attributes\validation;

abstract class ValidationRule
{
    protected string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    abstract public function validate(mixed $value): ?string;
}