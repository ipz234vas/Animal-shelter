<?php

namespace controllers;

use classes\attributes\routing\Get;
use classes\attributes\routing\Post;
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
        if (User::isUserLoggedIn())
            $this->redirect();
        return $this->view();
    }

    #[Post("login")]
    public function handleLogin(): array
    {
        $row = User::getById(1);
        $user = new User();
        $user->id = $row["id"];
        User::login($user);
        $this->redirect();
        return $this->view([
            'Test' => "ВИЙШЛО!"
        ]);
    }
}