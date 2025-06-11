<?php

namespace enums\auth;

enum Permission: string
{
    case ManageAnimals = 'manage_animals';
    case ManageArticles = 'manage_articles';
    case ManageReviews = 'manage_reviews';
    case ManageUsers = 'manage_users';
    case ManageApplications = 'manage_applications';

    public function label(): string
    {
        return match ($this) {
            self::ManageAnimals => "Керування тваринами",
            self::ManageArticles => "Керування статтями",
            self::ManageReviews => "Керування відгуками",
            self::ManageUsers => "Керування користувачами",
            self::ManageApplications => "Керування заявками",
        };
    }
}