<?php

namespace controllers;

use classes\Controller;

class HomeController extends Controller
{
    public function indexAction(?int $number): array
    {
        return $this->view('Index action', [
            'number' => $number,
        ]);
    }

    public function addAction(?int $number): array
    {
        return $this->view('Add action', [
            'number' => $number,
        ]);
    }
}