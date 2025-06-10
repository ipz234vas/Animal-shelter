<?php

namespace attributes\routing;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Patch extends Route
{
    public function __construct(string $action)
    {
        parent::__construct("PATCH", $action);
    }
}