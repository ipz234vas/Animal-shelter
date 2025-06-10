<?php

namespace controllers;

use classes\Controller;
use models\User;

class HomeController extends Controller
{
    public function indexAction(): array
    {
        return $this->view();
    }

    public function addAction(?int $number): array
    {
        return $this->view([
            'number' => $number,
        ]);
    }
}