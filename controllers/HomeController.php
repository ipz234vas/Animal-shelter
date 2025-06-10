<?php

namespace controllers;

use classes\attributes\Get;
use classes\attributes\Post;
use classes\Controller;

class HomeController extends Controller
{
    #[Get('index')]
    public function index(): array
    {
        return $this->view();
    }

    #[Post('add')]
    public function add(int $number): array
    {
        return $this->view([
            'number' => $number,
        ]);
    }
}