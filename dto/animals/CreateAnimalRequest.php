<?php

namespace dto\animals;

use attributes\validation\{Required, MinLength, Range};
use enums\animals\Sex;

class CreateAnimalRequest
{
    #[Required, MinLength(2)]
    public string $name;

    #[Required]
    public string|int $species_id = '';

    #[Required]
    public Sex $sex = Sex::Unknown;

    #[Range(0, 600, "Значення не може перевищувати 50 років")]
    public ?int $age_min_months = null;
    #[Range(0, 600, "Значення не може перевищувати 50 років")]
    public ?int $age_max_months = null;

    public ?string $description = null;
}
