<?php

namespace models;

use enums\applications\AdoptionStatus;
use enums\database\SQLOperator;

/**
 * @property int $id
 * @property int $user_id
 * @property int $animal_id
 * @property int $status
 * @property int $comment
 */
class Adoption extends Model
{
    public static string $table = 'adoption_applications';

    public static function forUser(int $uid): array
    {
        return static::asQuery()
            ->select()
            ->where('user_id', SQLOperator::Equal, $uid)
            ->fetch();
    }
}