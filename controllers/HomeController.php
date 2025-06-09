<?php

namespace controllers;

use classes\Controller;

class HomeController extends Controller
{
    public function indexAction(?int $number)
    {
        return $this->view('Index action', [
            'number' => $number,
        ]);
    }
    public function addAction(?int $number)
    {
        return $this->view('Add action', [
            'number' => $number,
        ]);
    }
}