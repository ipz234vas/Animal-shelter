<?php

namespace classes\attributes\routing;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Post extends Route
{
    public function __construct(string $action)
    {
        parent::__construct("POST", $action);
    }
}