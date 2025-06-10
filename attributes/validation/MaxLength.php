<?php

namespace attributes\validation;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class MaxLength extends ValidationRule
{
    private int $len;

    public function __construct(int $len, string $message = '')
    {
        $this->len = $len;
        parent::__construct($message ?: "Максимум {$len} символів.");
    }

    public function validate(mixed $value): ?string
    {
        $isMoreThanMaxLength = $value !== null && mb_strlen((string)$value) > $this->len;

        return $isMoreThanMaxLength ? $this->message : null;
    }
}