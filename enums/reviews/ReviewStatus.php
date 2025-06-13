<?php

namespace enums\reviews;

enum ReviewStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'В обробці',
            self::Accepted => 'Схвалено',
            self::Rejected => 'Відхилено',
        };
    }
}
