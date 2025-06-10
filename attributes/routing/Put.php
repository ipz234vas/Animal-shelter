<?php

namespace attributes\routing;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Put extends Route
{
    public function __construct(string $action)
    {
        parent::__construct("PUT", $action);
    }
}