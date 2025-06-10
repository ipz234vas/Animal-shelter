<?php

namespace attributes\validation;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class MinValue extends ValidationRule
{
    private int|float $min;

    public function __construct(int|float $min, string $message = '')
    {
        $this->min = $min;
        parent::__construct($message ?: "Мінімальне значення {$min}.");
    }

    public function validate(mixed $value): ?string
    {
        $isLessThenMin = $value !== null && $value < $this->min;

        return $isLessThenMin ? $this->message : null;
    }
}