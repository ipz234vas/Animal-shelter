<?php

namespace dto\listRequests;

class UsersListRequest extends BaseListRequest
{
    public array|string|null $permissions = null;

    protected const array ALLOWED_SORT = [
        'id', 'full_name', 'email'
    ];

    protected function customSanitize(): void
    {
        if (is_string($this->permissions)) {
            $this->permissions = [$this->permissions];
        }

        if (!is_array($this->permissions)) {
            $this->permissions = [];
        }

        $this->permissions = array_filter($this->permissions, 'is_string');
    }
}
