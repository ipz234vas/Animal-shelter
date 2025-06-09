<?php

namespace controllers;

use classes\Controller;
use models\User;

class HomeController extends Controller
{
    public function indexAction(): array
    {
        return $this->view('Index action');
    }

    public function addAction(?int $number): array
    {
        return $this->view('Add action', [
            'number' => $number,
        ]);
    }
}