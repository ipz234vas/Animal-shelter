<?php

namespace dto\account;

use attributes\validation\Required;

class ProfileDto
{
    #[Required]
    public string $full_name;
    public string $email;
}