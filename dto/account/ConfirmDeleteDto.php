<?php

namespace dto\account;

use attributes\validation\Required;
use attributes\validation\MinLength;

class ConfirmDeleteDto
{
    #[Required]
    public string $password;
}
