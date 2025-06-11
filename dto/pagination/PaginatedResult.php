<?php

namespace dto\pagination;

use ArrayIterator;
use IteratorAggregate;

final class PaginatedResult implements IteratorAggregate
{
    public readonly array $items;
    public readonly int $total;
    public readonly int $perPage;
    public readonly int $offset;

    public function __construct(array $items, int $total, int $perPage, int $offset)
    {
        $this->items = $items;
        $this->total = $total;
        $this->perPage = $perPage;
        $this->offset = $offset;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    public function lastPage(): int
    {
        return max(1, (int)ceil($this->total / $this->perPage));
    }

    public function currentPage(): int
    {
        $raw = (int)floor($this->offset / $this->perPage) + 1;

        return min(max(1, $raw), $this->lastPage());
    }
}
