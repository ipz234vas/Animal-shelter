<?php

namespace controllers;

use attributes\routing\Get;
use attributes\routing\Post;
use classes\Controller;
use dto\LoginDto;
use models\User;

class UsersController extends Controller
{
    #[Get("index")]
    public function index(): void
    {
        $this->redirect("users", "login");
    }

    #[Get("logout")]
    public function logout(): void
    {
        User::logout();
        $this->redirect('users', 'login');
    }

    #[Get("login")]
    public function login(string $next = ""): array
    {
        if (User::isUserLoggedIn()) {
            $this->redirect();
        }
        return $this->view([
            "next" => $next,
            "model" => new LoginDto()
        ]);
    }

    #[Post("login")]
    public function handleLogin(LoginDto $model, string $next = ''): array
    {
        if (!$this->modelState->isValid()) {
            return $this->view([
                "model" => $model,
                "errors" => $this->modelState->all(),
                "next" => $next
            ]);
        }

        $userRow = User::getByEmail($model->email);
        if (!$userRow || !User::checkPassword($model->password, $userRow['password_hash'])) {
            $this->modelState->add('password', 'Неправильний email або пароль');
            return $this->view([
                "model" => $model,
                "errors" => $this->modelState->all(),
                "next" => $next
            ]);
        }

        $user = new User();
        $user->id = $userRow['id'];
        User::login($user);

        $nextUrl = $next ? base64_decode($next) : '/';
        $this->redirectToPath($nextUrl);

        return [];
    }
}