<?php

namespace models;

use classes\Core;
use enums\database\SQLOperator;

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

    public static function getCurrentUser(): array|null
    {
        $userid = Core::getInstance()->session->get("user_id");
        return self::getById($userid);
    }

    public static function getPermissionsById(int $id): ?string
    {
        $row = User::asQuery()->select(["permissions"])->where(static::$primaryKey, SQLOperator::Equal, $id)->first();
        return $row["permissions"] ?? null;
    }

    public static function getByEmail(string $email): ?array
    {
        $row = User::asQuery()->select()->where("email", SQLOperator::Equal, $email)->first();
        return $row ?? null;
    }

    public static function checkPassword(string $password, string $password_hash): bool
    {
        return password_verify($password, $password_hash);
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}