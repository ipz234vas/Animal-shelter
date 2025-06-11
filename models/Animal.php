<?php

namespace models;

/**
 * @property int $id
 * @property string $name
 * @property int $species_id
 * @property string $sex
 * @property int|null $age_min_months
 * @property int|null $age_max_months
 * @property string|null $description
 * @property string|null $cover_image_url
 * @property string|null $intro_video_url
 */
class Animal extends Model
{
    public static string $table = 'animals';
}
