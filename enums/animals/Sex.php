<?php

namespace enums\animals;

enum Sex: string
{
    case Male = 'male';
    case Female = 'female';
    case Unknown = 'unknown';

    public function label(): string
    {
        return match ($this) {
            self::Male => 'Самець',
            self::Female => 'Самка',
            self::Unknown => 'Невідомо',
        };
    }
}
