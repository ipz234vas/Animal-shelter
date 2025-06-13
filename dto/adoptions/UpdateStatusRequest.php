<?php

namespace dto\adoptions;

use attributes\validation\Required;
use enums\applications\AdoptionStatus;

class UpdateStatusRequest
{
    #[Required] public int $id;
    #[Required] public AdoptionStatus $status;
}