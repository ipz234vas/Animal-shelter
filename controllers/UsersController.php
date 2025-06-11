<?php

namespace controllers;

use attributes\routing\Get;
use attributes\routing\Post;
use classes\Controller;
use dto\LoginDto;
use dto\RegisterDto;
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

    #[Get("register")]
    public function register(): array
    {
        if (User::isUserLoggedIn()) {
            $this->redirect();
        }
        return $this->view([
            "model" => new RegisterDto()
        ]);
    }

    #[Post("register")]
    public function handleRegister(RegisterDto $model): array
    {
        if (!$this->modelState->isValid()) {
            return $this->view([
                "model" => $model,
                "errors" => $this->modelState->all()
            ]);
        }

        if ($model->password !== $model->confirm_password) {
            $this->modelState->add('confirm_password', 'Паролі не співпадають');
            return $this->view([
                "model" => $model,
                "errors" => $this->modelState->all()
            ]);
        }

        if (User::getByEmail($model->email)) {
            $this->modelState->add('email', 'Користувач з такою поштою вже існує');
            return $this->view([
                "model" => $model,
                "errors" => $this->modelState->all()
            ]);
        }

        $user = new User();
        $user->full_name = $model->full_name;
        $user->password_hash = User::hashPassword($model->password);
        $user->email = $model->email;
        $user->permissions = "";
        $userId = $user->save();
        if (!$userId) {
            $this->modelState->add('email', 'Помилка реєстрації');
            return $this->view([
                "model" => $model,
                "errors" => $this->modelState->all()
            ]);
        }

        $user = new User();
        $user->id = $userId;
        User::login($user);

        $this->redirect();
        return [];
    }
}