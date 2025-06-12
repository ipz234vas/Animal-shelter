<?php

namespace dto\tags;

use attributes\validation\Required;

class TagUpdateRequest extends TagCreateRequest
{
    #[Required] public int $id;
}