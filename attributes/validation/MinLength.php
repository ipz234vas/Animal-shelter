<?php

namespace attributes\validation;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class MinLength extends ValidationRule
{
    private int $len;

    public function __construct(int $len, string $message = '')
    {
        $this->len = $len;
        parent::__construct($message ?: "Мінімум {$len} символів.");
    }

    public function validate(mixed $value): ?string
    {
        $isLessThanMinLength = $value !== null && mb_strlen((string)$value) < $this->len;

        return $isLessThanMinLength ? $this->message : null;
    }
}