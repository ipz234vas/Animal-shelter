<?php

namespace dto\species;

use attributes\validation\Required;

class SpeciesUpdateRequest extends SpeciesCreateRequest
{
    #[Required] public int $id;
}