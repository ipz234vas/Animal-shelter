<?php

namespace dto\account;

use attributes\validation\Required;

class ConfirmDeleteDto
{
    #[Required]
    public string $password;
}
