<?php

namespace dto;

use attributes\validation\Email;
use attributes\validation\MinLength;
use attributes\validation\Required;

class LoginDto
{
    #[Required, Email]
    public string $email;

    #[Required, MinLength(8)]
    public string $password;
}