<?php

namespace controllers;

use attributes\routing\Get;
use attributes\routing\Post;
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
    public function login($next = ""): array
    {
        if (User::isUserLoggedIn())
            $this->redirect();
        return $this->view([
            "next" => $next
        ]);
    }

    #[Post("login")]
    public function handleLogin($next): array
    {
        $row = User::getById(1);
        $user = new User();
        $user->id = $row["id"];
        User::login($user);
        $next = base64_decode($next ?? '');
        if ($next)
            $this->redirectToPath($next);
        else $this->redirect();

        return $this->view([
            'Test' => "ВИЙШЛО!"
        ]);
    }
}