<?php

namespace dto\account;

use attributes\validation\MinLength;
use attributes\validation\Required;

class ChangePasswordDto
{
    #[Required]
    public string $old_password;

    #[Required, MinLength(8)]
    public string $new_password;

    #[Required]
    public string $confirm_password;
}
