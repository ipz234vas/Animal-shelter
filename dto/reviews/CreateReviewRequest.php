<?php

namespace dto\reviews;

use attributes\validation\Required;

class CreateReviewRequest
{
    #[Required] public int $application_id;
    #[Required] public string $text = '';

    public array $images = [];
}