<?php

namespace attributes\routing;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    public string $method;
    public string $action;

    public function __construct(string $method, string $action)
    {
        $this->method = strtoupper($method);
        $this->action = $action;
    }
}