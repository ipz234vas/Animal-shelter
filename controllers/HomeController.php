<?php

namespace controllers;

use classes\attributes\auth\Authorize;
use classes\attributes\routing\Get;
use classes\Controller;
use classes\PermissionParser;
use enums\auth\Permission;
use models\User;

class HomeController extends Controller
{
    #[Get('index')]
    public function index(): array
    {
        return $this->view();
    }

    #[Authorize]
    #[Get('add')]
    public function add(int $number): array
    {
        return $this->view([
            'number' => $number,
        ]);
    }

    #[Authorize]
    #[Get('update')]
    public function update(int $number): array
    {
        $permissions = [Permission::ManageAnimals, Permission::ManageApplications, Permission::ManageArticles, Permission::ManageUsers, Permission::ManageReviews];
        $user = new User();
        $user->id = 1;
        $user->permissions = PermissionParser::toString($permissions);
        $user->save();
        return $this->view([
            'number' => $number,
        ]);
    }
}