<?php

namespace controllers;

use classes\attributes\Get;
use classes\Controller;
use models\User;

class UsersController extends Controller
{
    #[Get("logout")]
    public function logout(): void
    {
        User::logout();
        $this->redirect('users', 'login');
    }

    #[Get("login")]
    public function login(): array
    {
        return $this->view();
    }

    #[Get("login")]
    public function handle(): array
    {
        return $this->view([
            'Test' => "ВИЙШЛО!"
        ]);
    }
}