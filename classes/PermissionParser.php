<?php

namespace classes;

use enums\auth\Permission;

class PermissionParser
{
    public static function fromString(string $raw): array
    {
        $values = preg_split('/\s+/', trim($raw)) ?: [];
        return array_filter(array_map(Permission::tryFrom(...), $values));
    }

    public static function toString(array $permissions): string
    {
        return implode(' ', array_map(fn(Permission $p) => $p->value, $permissions));
    }
}
