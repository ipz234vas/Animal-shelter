<?php

namespace controllers;

use attributes\routing\Get;
use classes\Controller;
use models\User;

class UsersController extends Controller
{
    #[Get("index")]
    public function index(): array
    {
        $users = User::getAll();
        return $this->view(["users" => $users]);
    }
}