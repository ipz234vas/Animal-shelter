<?php

namespace controllers;

use attributes\auth\Authorize;
use attributes\routing\Get;
use attributes\routing\Post;
use classes\Controller;
use dto\account\ChangePasswordDto;
use dto\account\ConfirmDeleteDto;
use dto\account\ProfileDto;
use models\User;

#[Authorize]
class AccountController extends Controller
{
    #[Get("index")]
    public function index(): array
    {
        $this->redirect("account", "profile");
        return [];
    }

    #[Get("profile")]
    public function profile(): array
    {
        $userRow = User::getCurrentUser();

        $model = new ProfileDto();
        $model->full_name = $userRow['full_name'];
        $model->email = $userRow['email'];

        return $this->view([
            'model' => $model
        ]);
    }

    #[Post("profile")]
    public function handleProfile(ProfileDto $model): array
    {
        if (!$this->modelState->isValid()) {
            return $this->view([
                'model' => $model,
                'errors' => $this->modelState->all()
            ]);
        }

        $userRow = User::getCurrentUser();
        $user = new User();
        $user->id = $userRow['id'];
        $user->full_name = $model->full_name;
        $user->save();

        return $this->view([
            'model' => $model
        ]);
    }

    #[Get("password")]
    public function password(): array
    {
        return $this->view([
            'model' => new ChangePasswordDto()
        ]);
    }

    #[Post("password")]
    public function handlePassword(ChangePasswordDto $model): array
    {
        $userRow = User::getCurrentUser();

        if (!$this->modelState->isValid()) {
            return $this->view([
                'model' => $model,
                'errors' => $this->modelState->all()
            ]);
        }

        if (!User::checkPassword($model->old_password, $userRow['password_hash'])) {
            $this->modelState->add('old_password', 'Неправильний пароль');
        }

        if ($model->new_password !== $model->confirm_password) {
            $this->modelState->add('confirm_password', 'Паролі не співпадають');
        }

        if (!$this->modelState->isValid()) {
            return $this->view([
                'model' => $model,
                'errors' => $this->modelState->all()
            ]);
        }

        $user = new User();
        $user->id = $userRow['id'];
        $user->password_hash = User::hashPassword($model->new_password);
        $user->save();

        $this->redirect("account", "profile");

        return [];
    }

    #[Get("delete")]
    public function delete(): array
    {
        return $this->view([
            'model' => new ConfirmDeleteDto()
        ]);
    }

    #[Post("delete")]
    public function handleDelete(ConfirmDeleteDto $model): array
    {
        $userRow = User::getCurrentUser();

        if (!$this->modelState->isValid()) {
            return $this->view([
                'model' => $model,
                'errors' => $this->modelState->all()
            ]);
        }

        if (!User::checkPassword($model->password, $userRow['password_hash'])) {
            $this->modelState->add('password', 'Неправильний пароль');
            return $this->view([
                'model' => $model,
                'errors' => $this->modelState->all()
            ]);
        }

        User::deleteById($userRow['id']);
        User::logout();

        $this->redirect();

        return [];
    }
}
