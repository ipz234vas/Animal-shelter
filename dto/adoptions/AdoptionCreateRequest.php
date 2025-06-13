<?php

namespace dto\adoptions;

use attributes\validation\Required;

class AdoptionCreateRequest
{
    #[Required] public int $animal_id;
    public ?string $comment = null;
}