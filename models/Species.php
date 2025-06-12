<?php

namespace models;

use enums\database\SQLOperator;

/**
 * @property int $id
 * @property string $name
 */
class Species extends Model
{
    public static string $table = 'species';

    public static function existsByName(string $name): bool
    {
        return (bool)self::asQuery()
            ->select(['id'])
            ->where('name', SQLOperator::Equal, $name)
            ->first();
    }
}