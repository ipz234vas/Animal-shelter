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
    public int|string|null $age_min_months = null;
    #[Range(0, 600, "Значення не може перевищувати 50 років")]
    public int|string|null $age_max_months = null;

    public ?string $description = null;

    public array $tag_ids = [];

    public function normalize(): void
    {
        foreach (['age_min_months','age_max_months'] as $f) {
            if ($this->$f === '' || $this->$f === '0') $this->$f = null;
            if (is_string($this->$f)) $this->$f = (int)$this->$f;
        }
    }
}
