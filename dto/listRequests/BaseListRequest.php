<?php

namespace dto\listRequests;

use enums\database\SQLOrderDirection;

abstract class BaseListRequest
{
    public int $page = 1;
    public int $perPage = 3;
    public string $sortBy = 'id';
    public SQLOrderDirection $convertedDirection = SQLOrderDirection::Ascending;
    public string $direction = "";
    public ?string $query = null;

    protected const array ALLOWED_SORT = ['id'];
    public const array PER_PAGE_CHOICES = [5, 10, 25, 50];

    public function sanitize(): void
    {
        $this->page = max(1, $this->page);
        $this->perPage = min(max($this->perPage, 1), 100);

        $this->convertedDirection = $this->direction === SQLOrderDirection::Descending->value ? SQLOrderDirection::Descending : SQLOrderDirection::Ascending;
        $this->direction = $this->convertedDirection->value;

        if (!in_array($this->sortBy, static::ALLOWED_SORT, true)) {
            $this->sortBy = static::ALLOWED_SORT[0];
        }

        if (!in_array($this->perPage, self::PER_PAGE_CHOICES, true)) {
            $this->perPage = 10;
        }

        $this->customSanitize();
    }

    protected function customSanitize(): void
    {
    }
}
