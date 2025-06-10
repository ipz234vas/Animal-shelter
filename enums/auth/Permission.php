<?php

namespace enums\auth;

enum Permission: string
{
    case ManageAnimals = 'manage_animals';
    case ManageArticles = 'manage_articles';
    case ManageReviews = 'manage_reviews';
    case ManageUsers = 'manage_users';
    case ManageApplications = 'manage_applications';
}