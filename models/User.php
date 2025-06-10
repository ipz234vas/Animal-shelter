<?php

namespace models;

use classes\Core;

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

    public static function isUserLoggedIn(): bool
    {
        return !empty(Core::getInstance()->session->get("user_id"));
    }

    public static function login(User $user): void
    {
        Core::getInstance()->session->set("user_id", $user->id);
    }

    public static function logout(): void
    {
        Core::getInstance()->session->remove("user_id");
    }
}