<?php

namespace dto\adoptions;

use attributes\validation\Required;
use enums\applications\AdoptionStatus;

class CreateAdoptionRequest
{
    #[Required] public int $animal_id;
    public ?string $comment = null;

    public string $action = 'draft';

    public function toStatus(): AdoptionStatus
    {
        return $this->action === 'submit'
            ? AdoptionStatus::Pending
            : AdoptionStatus::Draft;
    }
}