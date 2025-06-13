<?php

namespace attributes\validation;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Range extends ValidationRule
{
    private int|float $min;
    private int|float $max;

    public function __construct(int|float $min, int|float $max, string $message = '')
    {
        $this->min = $min;
        $this->max = $max;
        parent::__construct($message ?: "Значення має бути між {$min} та {$max}.");
    }

    public function validate(mixed $value): ?string
    {
        $isOutOfRange = is_numeric($value) && ($value < $this->min || $value > $this->max);

        return $isOutOfRange ? $this->message : null;
    }
}