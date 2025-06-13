<?php

namespace dto\listRequests;

use attributes\validation\Range;
use enums\animals\Sex;

class AnimalsListRequest extends BaseListRequest
{
    public string|int|null $species_id = '';

    public string|null $sex = '';

    public array $tag_ids = [];

    #[Range(0, 600)] public int|string|null $age_min = null;
    #[Range(0, 600)] public int|string|null $age_max = null;

    public const array PER_PAGE_CHOICES = [6, 12, 18, 30];

    public ?bool $adopted = null;

    protected const array ALLOWED_SORT = ['id', 'name', 'updated_at'];

    public function speciesId(): ?int
    {
        return $this->species_id ?: null;
    }

    public function sexEnum(): ?Sex
    {
        return $this->sexEnum;
    }

    private ?Sex $sexEnum = null;

    protected function customSanitize(): void
    {
        if ($this->species_id === '' || $this->species_id === '0') {
            $this->species_id = null;
        } elseif (is_numeric($this->species_id)) {
            $this->species_id = (int)$this->species_id;
        } else {
            $this->species_id = null;
        }

        $this->sexEnum = Sex::tryFrom((string)$this->sex) ?: null;

        $this->tag_ids = array_values(
            array_filter(
                array_unique(array_map('intval', $this->tag_ids)),
                fn($id) => $id > 0
            )
        );

        foreach (['age_min','age_max'] as $f) {
            $val = $this->$f;
            if ($val === '' || $val === 0 || $val === '0') {     //  ← головне
                $this->$f = null;
            } elseif ($val !== null) {
                $this->$f = max(0, min(600, (int)$val));
            }
        }
    }
}
