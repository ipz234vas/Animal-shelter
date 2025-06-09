<?php

namespace controllers;

use classes\Controller;

class HomeController extends Controller
{
    public function addAction(?int $number)
    {
        return $this->view('Add action', [
            'number' => $number,
        ]);
    }
}