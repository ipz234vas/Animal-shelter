<?php

namespace attributes\validation;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Required extends ValidationRule
{
    public function __construct(string $message = "Це поле є обов'язковим!")
    {
        parent::__construct($message);
    }

    public function validate(mixed $value): ?string
    {
        $isEmpty = $value === null || (is_string($value) && trim($value) === '');

        return $isEmpty ? $this->message : null;
    }
}