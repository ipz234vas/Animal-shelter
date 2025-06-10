<?php

namespace attributes\validation;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Email extends ValidationRule
{
    public function __construct(string $message = 'Неправильний формат email.')
    {
        parent::__construct($message);
    }

    public function validate(mixed $value): ?string
    {
        $isNotEmail = ($value !== null) && !filter_var($value, FILTER_VALIDATE_EMAIL);

        return $isNotEmail ? $this->message : null;
    }
}