<?php

namespace models;
/**
 * @property int $id
 * @property string $email
 * @property string $password_hash
 * @property string $full_name
 * @property string $permissions
 */
class User extends Model
{
    public static string $table = "users";
}