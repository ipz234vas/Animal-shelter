<?php

namespace attributes\validation;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class MaxValue extends ValidationRule
{
    private int|float $max;

    public function __construct(int|float $max, string $message = '')
    {
        $this->max = $max;
        parent::__construct($message ?: "Максимальне значення {$max}.");
    }

    public function validate(mixed $value): ?string
    {
        $isMoreThenMax = $value !== null && $value > $this->max;

        return $isMoreThenMax ? $this->message : null;
    }
}