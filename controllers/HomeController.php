<?php

namespace controllers;

use attributes\routing\Get;
use classes\Controller;

class HomeController extends Controller
{
    #[Get('index')]
    public function index(): array
    {
        return $this->view();
    }
}