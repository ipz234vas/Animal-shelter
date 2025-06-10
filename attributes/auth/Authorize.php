<?php

namespace attributes\auth;

use Attribute;
use enums\auth\Permission;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Authorize
{
    /** @var Permission[] */
    public array $permissions;

    public function __construct(Permission|array|null $permissions = null)
    {
        if (is_null($permissions)) {
            $this->permissions = [];
        } else {
            $this->permissions = is_array($permissions) ? $permissions : [$permissions];
        }
    }

    public function isLoginOnly(): bool
    {
        return empty($this->permissions);
    }
}