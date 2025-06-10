<?php

namespace classes;

use attributes\validation\ValidationRule;
use ReflectionProperty;

class Validator
{
    public static function check(
        ModelState         $modelState,
        string             $path,
        mixed              $value,
        ReflectionProperty $prop): void
    {
        foreach ($prop->getAttributes() as $attr) {
            $rule = $attr->newInstance();
            if ($rule instanceof ValidationRule) {
                if ($msg = $rule->validate($value)) {
                    $modelState->add($path, $msg);
                }
            }
        }
    }
}