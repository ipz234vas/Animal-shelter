<?php

namespace controllers;

use classes\Controller;
use models\User;

class UsersController extends Controller
{
    public function logoutAction(): void
    {
        User::logout();
        $this->redirect('users', 'login');
    }

    public function loginAction(): array
    {
        return $this->view();
    }
}