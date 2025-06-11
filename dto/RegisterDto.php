<?php

namespace dto;

use attributes\validation\Required;
use attributes\validation\Email;
use attributes\validation\MinLength;

class RegisterDto
{
    #[Required]
    public string $full_name;

    #[Required, Email]
    public string $email;

    #[Required, MinLength(8)]
    public string $password;

    #[Required]
    public string $confirm_password;
}
