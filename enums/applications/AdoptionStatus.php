<?php

namespace enums\applications;

enum AdoptionStatus: string
{
    case Draft = "draft";
    case Pending = "pending";
    case Accepted = "accepted";
    case Rejected = "rejected";

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Чернетка',
            self::Pending => 'В обробці',
            self::Accepted => 'Схвалено',
            self::Rejected => 'Відхилено',
        };
    }
}