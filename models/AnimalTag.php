<?php

namespace models;

namespace models;

use enums\database\SQLOperator;

/**
 * @property int $animal_id
 * @property int $tag_id
 */
class AnimalTag extends Model
{
    public static string $table = 'animal_tags';
    protected static string $primaryKey = '';

    public static function attach(int $animalId, int $tagId): void
    {
        self::asQuery()->insert([
            'animal_id' => $animalId,
            'tag_id' => $tagId
        ])->execute();
    }

    public static function detachAll(int $animalId): void
    {
        self::asQuery()->delete()->where("animal_id", SQLOperator::Equal, $animalId)->execute();
    }
}
