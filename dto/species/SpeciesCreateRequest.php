<?php

namespace dto\species;

use attributes\validation\MaxLength;
use attributes\validation\MinLength;
use attributes\validation\Required;

class SpeciesCreateRequest
{
    #[Required, MinLength(2), MaxLength(40)]
    public string $name;
}