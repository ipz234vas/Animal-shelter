<?php

namespace attributes\binding;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Collection
{
    public string $itemType;

    public function __construct(string $itemType)
    {
        $this->itemType = $itemType;
    }
}