<?php

namespace attributes\validation;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Pattern extends ValidationRule
{
    private string $regex;

    public function __construct(string $regex, string $message = '')
    {
        $this->regex = $regex;
        parent::__construct($message ?: 'Поле не відповідає формату.');
    }

    public function validate(mixed $value): ?string
    {
        $isWrongPattern = $value !== null && !preg_match($this->regex, (string)$value);

        return $isWrongPattern ? $this->message : null;
    }
}